<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen py-12 px-4">
    <div class="container mx-auto max-w-4xl">
        <!-- Header Card -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-1">Tambah Data Mahasiswa</h1>
                        <p class="text-blue-100 text-sm">Sistem Manajemen Data RDF</p>
                    </div>
                    <a href="{{ route('rdf.index') }}" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg backdrop-blur-sm transition duration-300 text-sm font-medium">
                        ← Kembali
                    </a>
                </div>
            </div>

            <!-- Form Section -->
            <div class="p-8">
                <!-- Client-side success placeholder (shown by JS) -->
                <div class="mb-6 rounded-xl bg-emerald-50 border-l-4 border-emerald-500 p-4 hidden" id="successMsg">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-emerald-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-emerald-800 font-medium">Data berhasil disimpan!</span>
                    </div>
                </div>

                {{-- Server-side flash messages --}}
                @if(session('success'))
                    <div class="mb-6 rounded-lg bg-emerald-50 border-l-4 border-emerald-500 p-4">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-emerald-400 mr-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div class="text-emerald-800 text-sm font-medium">{{ session('success') }}</div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 rounded-lg bg-red-50 border-l-4 border-red-500 p-4">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-red-400 mr-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div class="text-red-700 text-sm font-medium">{{ session('error') }}</div>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 rounded-lg bg-red-50 border-l-4 border-red-500 p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-red-400 mr-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
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

                <form action="{{ route('rdf.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <!-- Nama Field -->
                    <div class="group">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                                Nama Lengkap
                            </span>
                        </label>
                        <input type="text" name="name" placeholder="Masukkan nama lengkap mahasiswa"
                            class="w-full border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 rounded-xl px-4 py-3 transition duration-300 outline-none" required>
                    </div>

                    <!-- NIM Field -->
                    <div class="group">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z" clip-rule="evenodd"/>
                                </svg>
                                Nomor Induk Mahasiswa (NIM)
                            </span>
                        </label>
                        <input type="text" name="studentId" placeholder="Contoh: 12345678"
                            class="w-full border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 rounded-xl px-4 py-3 transition duration-300 outline-none" required>
                    </div>

                    <!-- Grid Layout untuk Program Studi & Universitas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Program Studi -->
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                    </svg>
                                    Program Studi
                                </span>
                            </label>
                            <input type="text" name="studyProgram" placeholder="Contoh: Teknik Informatika"
                                class="w-full border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 rounded-xl px-4 py-3 transition duration-300 outline-none" required>
                        </div>

                        <!-- Universitas -->
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
                                    </svg>
                                    Universitas
                                </span>
                            </label>
                            <input type="text" name="university" placeholder="Contoh: Universitas Indonesia"
                                class="w-full border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 rounded-xl px-4 py-3 transition duration-300 outline-none" required>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-indigo-500 rounded-xl p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-indigo-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-indigo-900">Informasi Penting</p>
                                <p class="text-xs text-indigo-700 mt-1">Data yang disimpan akan diproses sebagai entri RDF. Pastikan semua informasi yang dimasukkan sudah benar dan sesuai dengan kebijakan kampus.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white font-semibold px-6 py-3.5 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-300 flex items-center justify-center group">
                            <svg class="w-5 h-5 mr-2 group-hover:rotate-12 transition-transform duration-300" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z"/>
                            </svg>
                            Simpan Data
                        </button>
                        <button type="reset" class="flex-none bg-white border-2 border-gray-200 text-gray-700 font-semibold px-6 py-3.5 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition duration-300">
                            Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center">
            <p class="text-sm text-gray-600 flex items-center justify-center">
                <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">Sistem RDF 2025</span>
                <span class="mx-2">•</span>
                <span class="text-gray-500">Dibuat oleh Rizky</span>
            </p>
        </div>
    </div>

    <script>
        // Form validation & animation
        const form = document.querySelector('form');
        const inputs = document.querySelectorAll('input[required]');

        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.classList.add('border-red-300');
                    this.classList.remove('border-gray-200');
                } else {
                    this.classList.remove('border-red-300');
                    this.classList.add('border-green-300');
                }
            });

            input.addEventListener('focus', function() {
                this.classList.remove('border-red-300', 'border-green-300');
                this.classList.add('border-indigo-500');
            });
        });

        form.addEventListener('submit', function() {
            // Validate all required fields
            let isValid = true;
            inputs.forEach(input => {
                if (input.value.trim() === '') {
                    input.classList.add('border-red-300');
                    isValid = false;
                }
            });

            if (!isValid) {
                return false; // Prevent submission if validation fails
            }

            // If valid, show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menyimpan...
            `;

            // Let the form submit naturally to the server
            return true;
        });
    </script>
</body>
</html>
