<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data RDF</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center">
<div class="max-w-lg w-full bg-white rounded-2xl shadow p-6">
    <h1 class="text-xl font-bold mb-4 text-slate-800">✏️ Edit Data Mahasiswa</h1>

    <form method="POST" action="{{ route('rdf.update', urlencode($data['name'])) }}">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium">Nama</label>
                <input name="name" value="{{ $data['name'] }}" readonly class="w-full border px-3 py-2 rounded-lg bg-gray-100" />
            </div>

            <div>
                <label class="block text-sm font-medium">NIM</label>
                <input name="studentId" value="{{ $data['studentId'] }}" class="w-full border px-3 py-2 rounded-lg" />
            </div>

            <div>
                <label class="block text-sm font-medium">Program Studi</label>
                <input name="studyProgram" value="{{ $data['studyProgram'] }}" class="w-full border px-3 py-2 rounded-lg" />
            </div>

            <div>
                <label class="block text-sm font-medium">Universitas</label>
                <input name="university" value="{{ $data['university'] }}" class="w-full border px-3 py-2 rounded-lg" />
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 w-full">Simpan Perubahan</button>
        </div>
    </form>
</div>
</body>
</html>

