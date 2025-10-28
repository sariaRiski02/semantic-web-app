<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa RDF</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
    <div class="container mx-auto py-12 px-6">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-slate-800">📘 Data Mahasiswa RDF</h1>
            <a href="{{ url('/rdf/create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">+ Tambah Data</a>
        </div>

        {{-- Tampilkan pesan sukses --}}
        @if(session('success'))
            <div class="mb-6 rounded-lg bg-emerald-50 border-l-4 border-emerald-500 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-emerald-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tampilkan pesan error --}}
        @if(session('error'))
            <div class="mb-6 rounded-lg bg-red-50 border-l-4 border-red-500 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tampilkan validation errors --}}
        @if($errors->any())
            <div class="mb-6 rounded-lg bg-red-50 border-l-4 border-red-500 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Terdapat beberapa kesalahan:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- 🔍 Form pencarian tunggal -->
        <form action="{{ route('rdf.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 mb-6">
            <input type="text" name="search" value="{{ $search ?? '' }}"
                placeholder="Cari berdasarkan nama, NIM, program studi, atau universitas..."
                class="border border-slate-300 rounded-lg px-3 py-2 w-full sm:w-1/2 focus:ring-2 focus:ring-blue-400" />

            <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 shadow">
                🔍 Cari
            </button>

            <a href="{{ route('rdf.index') }}"
                class="bg-gray-100 border border-slate-300 text-slate-700 px-4 py-2 rounded-lg hover:bg-gray-200">
                Reset
            </a>
        </form>


        @if ($message)
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 p-4 rounded-lg">
                {{ $message }}
            </div>
        @elseif (count($data) > 0)
            <div class="overflow-x-auto bg-white shadow-lg rounded-2xl">
                <table class="min-w-full text-sm text-slate-700">
                    <thead class="bg-slate-100 text-slate-900 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama</th>
                            <th class="px-4 py-3 text-left">NIM</th>
                            <th class="px-4 py-3 text-left">Program Studi</th>
                            <th class="px-4 py-3 text-left">Universitas</th>
                            <th class="px-4 py-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($data as $item)
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="px-4 py-2 font-medium">{{ $item['name'] }}</td>
                        <td class="px-4 py-2">{{ $item['studentId'] }}</td>
                        <td class="px-4 py-2">{{ $item['studyProgram'] }}</td>
                        <td class="px-4 py-2">{{ $item['university'] }}</td>
                        <td class="px-4 py-2 flex gap-2">
                            <a href="{{ route('rdf.edit', urlencode($item['name'])) }}"
                            class="text-blue-600 hover:underline">Edit</a>

                            <form action="{{ route('rdf.destroy', urlencode($item['name'])) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-gray-50 border border-gray-200 text-gray-600 p-4 rounded-lg">
                Tidak ada data mahasiswa ditemukan.
            </div>
        @endif

        <p class="text-xs text-slate-400 mt-8 text-center">© 2025 Semantic Web App — Dibuat oleh Rizky</p>
    </div>
</body>
</html>
