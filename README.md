# semantic-web-app

Dokumentasi teknis untuk menjalankan aplikasi "semantic-web-app".

Ringkasan
--
Proyek ini adalah tugas untuk mata kuliah Semantik Web yang dibuat sebagai contoh sederhana untuk menyimpan dan mengambil data mahasiswa dalam format RDF (Turtle) menggunakan Laravel (PHP) dan EasyRdf. Data juga dapat dikirim dan di-query menggunakan Apache Jena Fuseki (SPARQL endpoint).

Isi dokumen ini
--
- Prasyarat
- Menjalankan aplikasi Laravel
- Memasang dan menyiapkan Apache Jena Fuseki
- Menulis RDF (Turtle) dengan PHP menggunakan EasyRdf
- Mengirim (upload) file Turtle ke Fuseki
- Menjalankan query SPARQL dari aplikasi (JSON results)
- Troubleshooting dan tips

Prasyarat
--
- PHP 8.x (sesuaikan dengan requirement Laravel di repo)
- Composer
- Java (untuk menjalankan Fuseki)
- Apache Jena Fuseki (server)
- ekstensi/ package PHP: EasyRdf (sudah terdaftar di composer.json pada repo ini)

Install dependency PHP
--
Di root proyek jalankan:

```powershell
composer install
```

Konfigurasi environment
--
Salin file `.env.example` ke `.env` (jika belum ada) dan atur APP_KEY:

```powershell
copy .env.example .env
php artisan key:generate
```

Tambahkan konfigurasi Fuseki di `.env`. Penting: `FUSEKI_ENDPOINT` harus berisi base URL untuk dataset Anda (contoh: `http://localhost:3030/dataset` jika dataset bernama `dataset`).

Contoh .env lines:

```
FUSEKI_ENDPOINT=http://localhost:3030/dataset
# jika Anda menaruh Fuseki di host/port lain, ubah sesuai
```

Menjalankan aplikasi Laravel (dev)
--
Jalankan server pengembangan Laravel:

```powershell
php artisan serve
```

Apache Jena Fuseki — instalasi singkat
--
1. Download Fuseki (Apache Jena) dari https://jena.apache.org/download/index.cgi — ambil Fuseki server distribution.
2. Ekstrak ke folder pilihan Anda.
3. Jalankan Fuseki (Windows / PowerShell):

```powershell
# jalankan dari direktori hasil ekstrak, contoh:
# untuk menjalankan dengan antarmuka admin dan kemampuan update
.\fuseki-server --update --mem /dataset
```

Atau jalankan `fuseki-server` tanpa opsi dan create dataset lewat UI.

4. Buka admin UI: `http://localhost:3030` → Manage Datasets.
5. Jika Anda ingin endpoint `http://localhost:3030/dataset`, buat dataset dengan name `dataset` (pilih In-memory atau Persistent sesuai kebutuhan). Setelah dibuat, endpoint SPARQL akan berada di `http://localhost:3030/dataset/sparql` dan endpoint upload di `http://localhost:3030/dataset/data`.

Upload Turtle (manual) ke Fuseki
--
Contoh upload file Turtle (curl):

```powershell
curl -X POST "http://localhost:3030/dataset/data" -F "file=@data.ttl;type=text/turtle"
```

Jika upload berhasil, Anda akan melihat response sukses (HTTP 200/204). Jika dataset belum ada, endpoint akan gagal (404 atau 4xx).

Menulis RDF (Turtle) menggunakan PHP + EasyRdf
--
EasyRdf memudahkan pembuatan graph, resource, dan serialisasi ke Turtle.

Contoh kode (Laravel controller / plain PHP):

```php
use EasyRdf\Graph;

$graph = new Graph('http://example.org/');
// daftarkan prefix agar output lebih rapi
$graph->setNamespace('ex', 'http://example.org/');
$graph->setNamespace('foaf', 'http://xmlns.com/foaf/0.1/');

$studentUri = 'http://example.org/student/' . urlencode('Budi_Santoso');
$student = $graph->resource($studentUri, 'foaf:Person');
$student->add('foaf:name', 'Budi Santoso');
$student->add('ex:studentId', '12345678');
$student->add('ex:studyProgram', 'Teknik Informatika');
$student->add('ex:university', 'Universitas Contoh');

$ttl = $graph->serialise('turtle');
file_put_contents(storage_path('app/data.ttl'), $ttl);
```

Penjelasan singkat:
- `Graph` membuat objek graph RDF.
- `resource($uri, $type)` membuat resource/subject dengan tipe RDF (mis. foaf:Person).
- `add($predicate, $value)` menambahkan triple predicate->value.
- `serialise('turtle')` menghasilkan representasi Turtle.

Contoh Turtle minimal yang dihasilkan:

```turtle
@prefix ex: <http://example.org/> .
@prefix foaf: <http://xmlns.com/foaf/0.1/> .

<http://example.org/student/Budi_Santoso> a foaf:Person ;
	foaf:name "Budi Santoso" ;
	ex:studentId "12345678" ;
	ex:studyProgram "Teknik Informatika" ;
	ex:university "Universitas Contoh" .
```

Mengirim (upload) Turtle ke Fuseki dari Laravel
--
Setelah menghasilkan Turtle (`$ttl` string atau file `data.ttl`), Anda bisa meng-upload ke Fuseki menggunakan HTTP multipart/form-data (field `file`). Contoh dengan Laravel Http client:

```php
use Illuminate\Support\Facades\Http;

$endpoint = env('FUSEKI_ENDPOINT') . '/data'; // mis: http://localhost:3030/dataset/data

$response = Http::asMultipart()->post($endpoint, [
	[
		'name' => 'file',
		'contents' => $ttl, // atau fopen($path, 'r')
		'filename' => 'data.ttl',
	],
]);

if ($response->failed()) {
	// log atau kirim pesan error ke UI
	// dd($response->status(), $response->body());
}
```

Atau gunakan curl (dari terminal):

```powershell
curl -X POST "http://localhost:3030/dataset/data" -F "file=@path\to\data.ttl;type=text/turtle"
```

Query SPARQL dari aplikasi
--
Contoh aplikasi mengirim query ke endpoint SPARQL dan meminta hasil JSON (controller di repo menggunakan Accept: application/sparql-results+json):

```php
$endpoint = env('FUSEKI_ENDPOINT') . '/sparql';
$query = "PREFIX foaf: <http://xmlns.com/foaf/0.1/>\nSELECT ?name WHERE { ?s a foaf:Person ; foaf:name ?name } LIMIT 10";

$response = Http::withHeaders(['Accept' => 'application/sparql-results+json'])
	->asForm()
	->post($endpoint, ['query' => $query]);

$json = $response->json();
$bindings = $json['results']['bindings'] ?? [];
foreach ($bindings as $b) {
	$name = $b['name']['value'] ?? null;
}
```

Hal-hal yang sering bermasalah & troubleshooting
--
- Dataset tidak ada: jika Anda mem-post ke `/dataset/data` tapi dataset belum dibuat, Fuseki akan mengembalikan error. Solusi: buat dataset lewat UI atau jalankan `fuseki-server --update --mem /dataset`.
- Response bukan JSON: jika Accept header tidak diset atau server mengembalikan HTML/error page, `$response->json()` bisa mengembalikan null. Untuk debug sementara gunakan `dd($response->status(), $response->headers(), $response->body())`.
- Prefix/predicate tidak cocok: pastikan predikat yang Anda query (ex:studentId) sama dengan yang ada di data TTL (namespace dan URI harus cocok). Jika data TTL menggunakan `http://example.org/` lalu gunakan prefix `PREFIX ex: <http://example.org/>` di query dan `ex:studentId` (tanpa tanda &lt;&gt;).
- File permission: pastikan `storage/` dapat ditulis oleh PHP (Laravel menyimpan sementara `data.ttl` di `storage/app/`).

Tes cepat
--
1. Pastikan Fuseki berjalan dan dataset tersedia di `http://localhost:3030/{DATASET}`.
2. Buat data lewat form `http://127.0.0.1:8000/rdf/create`.
3. Periksa response dari controller (gunakan logs atau dd sementara) dan cek di Fuseki UI apakah triple bertambah.

Contoh perintah debugging (PowerShell):

```powershell
# cek dataset yang tersedia
Invoke-WebRequest -Uri http://localhost:3030/$/datasets | Select-Object -ExpandProperty Content

# kirim query sederhana via curl
curl -X POST -H "Accept: application/sparql-results+json" -d "query=SELECT * WHERE { ?s ?p ?o } LIMIT 5" http://localhost:3030/dataset/sparql
```

Penutup
--
Dokumentasi ini menyediakan langkah-langkah praktis untuk: menyiapkan Fuseki, membuat RDF Turtle dengan EasyRdf di PHP, meng-upload ke Fuseki, dan menjalankan query SPARQL dari aplikasi Laravel. Jika Anda ingin, saya bisa menambahkan:

- Skrip PowerShell/Batch untuk otomatis membuat dataset Fuseki
- Contoh unit/integration test untuk controller yang melakukan upload dan query
- Template `.env.example` yang menunjukkan `FUSEKI_ENDPOINT`

Jika mau saya bisa langsung menambahkan variabel `FUSEKI_ENDPOINT` ke `.env.example` dan memperbarui controller untuk menggunakan `env()` dengan fallback.

# RDF (Resource Description Framework)
