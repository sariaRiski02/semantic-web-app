<?php

namespace App\Http\Controllers;

use EasyRdf\Graph;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RDFController extends Controller
{

    public function viewData(Request $request)
    {
        $endpoint = env('FUSEKI_ENDPOINT') . '/sparql';

        // Ambil input pencarian (opsional)
        $search = trim($request->input('search', ''));


        // Query dasar
        $query = '
                PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                SELECT DISTINCT ?name ?studentId ?studyProgram ?university
                WHERE {
                    ?person a foaf:Person ;
                            foaf:name ?name ;
                            <ex:studentId> ?studentId ;
                            <ex:studyProgram> ?studyProgram ;
                            <ex:university> ?university .
            ';

        // Kalau pengguna mengisi pencarian, tambahkan FILTER
        if ($search) {
            $escaped = addslashes($search); // hindari karakter aneh di SPARQL
            $query .= "
                FILTER (
                    CONTAINS(LCASE(?name), LCASE('{$escaped}')) ||
                    CONTAINS(LCASE(?studentId), LCASE('{$escaped}')) ||
                    CONTAINS(LCASE(?studyProgram), LCASE('{$escaped}')) ||
                    CONTAINS(LCASE(?university), LCASE('{$escaped}'))
                )
            ";
        }

        $query .= '} ORDER BY ?name';

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/sparql-results+json'
            ])->asForm()->post($endpoint, ['query' => $query]);


            if ($response->failed()) {
                return view('rdf.table', [
                    'data' => [],
                    'message' => 'Gagal mengambil data dari Fuseki.'
                ]);
            }

            $json = $response->json();
            $data = $json['results']['bindings'] ?? [];

            $rows = [];
            foreach ($data as $item) {
                $rows[] = [
                    'name' => $item['name']['value'] ?? '-',
                    'studentId' => $item['studentId']['value'] ?? '-',
                    'studyProgram' => $item['studyProgram']['value'] ?? '-',
                    'university' => $item['university']['value'] ?? '-',
                ];
            }

            return view('rdf.table', [
                'data' => $rows,
                'message' => count($rows) ? null : 'Tidak ada data RDF ditemukan di Fuseki.',
                'search' => $search,
            ]);
        } catch (\Exception $e) {
            return view('rdf.table', [
                'data' => [],
                'message' => 'Tidak dapat terhubung ke Fuseki. Pastikan server Fuseki berjalan di port 3030.',
                'search' => $search,
            ]);
        }
    }

    public function create()
    {
        return view('rdf.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'studentId' => 'required',
            'studyProgram' => 'required',
            'university' => 'required',
        ]);

        // --- 1️⃣ Buat graph RDF baru ---
        $graph = new Graph('http://example.org/');
        $studentUri = 'ex:' . str_replace(' ', '_', $request->name);
        $student = $graph->resource($studentUri, 'foaf:Person');

        $student->add('foaf:name', $request->name);
        $student->add('ex:studentId', $request->studentId);
        $student->add('ex:studyProgram', $request->studyProgram);
        $student->add('ex:university', $request->university);

        // --- 2️⃣ Ambil path file RDF lokal ---
        $ttlPath = storage_path('app/data.ttl');

        // --- 3️⃣ Kalau sudah ada data lama, load dulu agar tidak ditimpa ---
        if (file_exists($ttlPath)) {
            $graph->parse(file_get_contents($ttlPath), 'turtle');
        }

        // --- 4️⃣ Simpan RDF ke file lokal ---
        $ttl = $graph->serialise('turtle');
        file_put_contents($ttlPath, $ttl);

        // --- 5️⃣ Kirim RDF ke Apache Jena Fuseki ---
        try {
            $response = Http::asMultipart()->post(env('FUSEKI_ENDPOINT') . '/data', [
                [
                    'name' => 'file',
                    'contents' => file_get_contents($ttlPath),
                    'filename' => 'data.ttl',
                ],
            ]);

            if ($response->failed()) {
                return back()->with('error', 'Gagal mengirim data ke Fuseki.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Tidak dapat terhubung ke Fuseki. Pastikan Fuseki berjalan di port 3030.');
        }

        // --- 6️⃣ Redirect ke halaman daftar RDF ---
        return redirect('/rdf')->with('success', 'Data berhasil disimpan dan dikirim ke Fuseki!');
    }

    public function edit($name)
    {
        $endpoint = env('FUSEKI_ENDPOINT') . '/sparql';

        // Decode agar Todd+Stokes -> Todd Stokes
        $name = urldecode($name);
        $escaped = addslashes($name);

        $query = "
            PREFIX foaf: <http://xmlns.com/foaf/0.1/>
            SELECT ?studentId ?studyProgram ?university
            WHERE {
                ?person a foaf:Person ;
                        foaf:name ?n ;
                        <ex:studentId> ?studentId ;
                        <ex:studyProgram> ?studyProgram ;
                        <ex:university> ?university .
                FILTER(LCASE(STR(?n)) = LCASE(\"{$escaped}\"))
            }
        ";

        $response = Http::asForm()
            ->withHeaders(['Accept' => 'application/sparql-results+json'])
            ->post($endpoint, ['query' => $query]);

        $json = $response->json();

        $result = $json['results']['bindings'][0] ?? null;

        if (!$result) {
            return redirect()->route('rdf.index')->with('error', 'Data tidak ditemukan di Fuseki.');
        }

        $data = [
            'name' => $name,
            'studentId' => $result['studentId']['value'] ?? '',
            'studyProgram' => $result['studyProgram']['value'] ?? '',
            'university' => $result['university']['value'] ?? '',
        ];

        return view('rdf.edit', ['data' => $data]);
    }


    public function update(Request $request, $name)
    {
        $endpoint = env('FUSEKI_ENDPOINT') . '/update';

        $oldName = urldecode($name);
        $escapedName = addslashes($oldName);
        $newId = addslashes($request->studentId);
        $newStudy = addslashes($request->studyProgram);
        $newUni = addslashes($request->university);

        $updateQuery = "
            PREFIX foaf: <http://xmlns.com/foaf/0.1/>
            DELETE {
                ?person <ex:studentId> ?sid ;
                        <ex:studyProgram> ?sp ;
                        <ex:university> ?univ .
            }
            INSERT {
                ?person <ex:studentId> \"{$newId}\" ;
                        <ex:studyProgram> \"{$newStudy}\" ;
                        <ex:university> \"{$newUni}\" .
            }
            WHERE {
                ?person a foaf:Person ;
                        foaf:name ?n ;
                        <ex:studentId> ?sid ;
                        <ex:studyProgram> ?sp ;
                        <ex:university> ?univ .
                FILTER(LCASE(STR(?n)) = LCASE(\"{$escapedName}\"))
            }
        ";

        $response = Http::asForm()->post($endpoint, ['update' => $updateQuery]);

        if ($response->failed()) {
            return back()->with('error', 'Gagal memperbarui data di Fuseki.');
        }

        return redirect()->route('rdf.index')->with('success', "Data '{$oldName}' berhasil diperbarui di Fuseki.");
    }

    public function destroy($name)
    {
        $endpoint = env('FUSEKI_ENDPOINT') . '/update';
        $name = urldecode($name);
        $escaped = addslashes($name);

        // 🔹 1. Hapus di Fuseki
        $deleteQuery = "
            PREFIX foaf: <http://xmlns.com/foaf/0.1/>
            PREFIX ex: <ex:>
            DELETE {
                ?person a foaf:Person ;
                        foaf:name ?n ;
                        ex:studentId ?sid ;
                        ex:studyProgram ?sp ;
                        ex:university ?univ .
            }
            WHERE {
                ?person a foaf:Person ;
                        foaf:name ?n ;
                        ex:studentId ?sid ;
                        ex:studyProgram ?sp ;
                        ex:university ?univ .
                FILTER(LCASE(STR(?n)) = LCASE(\"{$escaped}\"))
            }
        ";

        $response = Http::asForm()->post($endpoint, ['update' => $deleteQuery]);

        if ($response->failed()) {
            dd($response->body()); // lihat error Fuseki kalau gagal
            return back()->with('error', 'Gagal menghapus data di Fuseki.');
        }

        // 🔹 2. Hapus juga dari file data.ttl lokal
        $ttlPath = storage_path('app/data.ttl');
        if (file_exists($ttlPath)) {
            $graph = new \EasyRdf\Graph();
            $graph->parse(file_get_contents($ttlPath), 'turtle');

            foreach ($graph->resources() as $uri => $res) {
                $resName = $res->get('foaf:name');
                if ($resName && strcasecmp(trim($resName), trim($name)) === 0) {
                    foreach ($res->propertyUris() as $predicate) {
                        foreach ($res->all($predicate) as $object) {
                            $graph->delete($res, $predicate, $object);
                        }
                    }
                }
            }

            file_put_contents($ttlPath, $graph->serialise('turtle'));
        }

        return redirect()->route('rdf.index')->with('success', "Data '{$name}' berhasil dihapus dari Fuseki dan file lokal.");
    }
}
