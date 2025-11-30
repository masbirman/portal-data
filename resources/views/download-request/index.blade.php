@extends('public.layout')

@section('title', 'Pengajuan Unduh Data - Portal Data AN-TKA Disdik Sulteng')

@section('content')
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="bg-slate-900 rounded-lg shadow-lg p-8 mb-6 text-white">
            <h1 class="text-3xl font-bold mb-2 flex items-center gap-3">
                <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                Pengajuan Unduh Data
            </h1>
            <p class="text-slate-300">
                Untuk mengunduh data statistik pendidikan, silakan isi formulir di bawah ini. Tim kami akan meninjau
                permintaan Anda dan mengirimkan link download jika disetujui.
            </p>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Terdapat kesalahan pada form:
                            </h3>
                            <ul class="mt-2 text-sm text-red-700 dark:text-red-300 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('download-request.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Nama -->
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Link download akan dikirim ke email ini</p>
                </div>

                <!-- Instansi -->
                <div>
                    <label for="instansi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Instansi/Organisasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="instansi" id="instansi" value="{{ old('instansi') }}" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Tujuan Penggunaan -->
                <div>
                    <label for="tujuan_penggunaan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tujuan Penggunaan Data <span class="text-red-500">*</span>
                    </label>
                    <textarea name="tujuan_penggunaan" id="tujuan_penggunaan" rows="4" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">{{ old('tujuan_penggunaan') }}</textarea>
                </div>

                <!-- Jenis Data -->
                <div>
                    <label for="data_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Jenis Data <span class="text-red-500">*</span>
                    </label>
                    <select name="data_type" id="data_type" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">Pilih Jenis Data</option>
                        <option value="asesmen_nasional" {{ old('data_type') == 'asesmen_nasional' ? 'selected' : '' }}>
                            Asesmen Nasional (ANBK)</option>
                        <option value="survei_lingkungan_belajar"
                            {{ old('data_type') == 'survei_lingkungan_belajar' ? 'selected' : '' }}>Survei Lingkungan
                            Belajar</option>
                        <option value="tes_kemampuan_akademik"
                            {{ old('data_type') == 'tes_kemampuan_akademik' ? 'selected' : '' }}>Tes Kemampuan Akademik
                            (TKA)</option>
                    </select>
                </div>

                <!-- Tahun -->
                <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tahun <span class="text-red-500">*</span>
                    </label>
                    <select name="tahun" id="tahun" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">Pilih Tahun</option>
                        @for ($year = date('Y'); $year >= 2023; $year--)
                            <option value="{{ $year }}" {{ old('tahun') == $year ? 'selected' : '' }}>
                                {{ $year }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Wilayah -->
                <div>
                    <label for="wilayah_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Wilayah <span class="text-red-500">*</span>
                    </label>
                    <select name="wilayah_id" id="wilayah_id" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">Pilih Wilayah</option>
                        <option value="0" {{ old('wilayah_id') == '0' ? 'selected' : '' }}>Semua Wilayah</option>
                        @foreach ($wilayahs as $wilayah)
                            <option value="{{ $wilayah->id }}" {{ old('wilayah_id') == $wilayah->id ? 'selected' : '' }}>
                                {{ $wilayah->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Jenjang -->
                <div>
                    <label for="jenjang_pendidikan_id"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Jenjang Pendidikan <span class="text-red-500">*</span>
                    </label>
                    <select name="jenjang_pendidikan_id" id="jenjang_pendidikan_id" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">Pilih Jenjang</option>
                        <option value="0" {{ old('jenjang_pendidikan_id') == '0' ? 'selected' : '' }}>Semua Jenjang
                        </option>
                        @foreach ($jenjangs as $jenjang)
                            <option value="{{ $jenjang->id }}"
                                {{ old('jenjang_pendidikan_id') == $jenjang->id ? 'selected' : '' }}>
                                {{ $jenjang->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit Button -->
                <!-- Turnstile -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Verifikasi <span class="text-red-500">*</span>
                    </label>
                    <div class="cf-turnstile" data-sitekey="{{ config('turnstile.turnstile_site_key') }}"
                        data-theme="light"></div>
                </div>

                <div class="flex items-center justify-between pt-4">
                    <a href="{{ route('public.landing') }}"
                        class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                        ← Kembali
                    </a>
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl">
                        Kirim Permintaan
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Box -->
        <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Informasi Penting</h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Permintaan akan ditinjau oleh tim kami dalam 1-2 hari kerja</li>
                            <li>Cek status pengajuan melalui menu <a href="{{ route('download-request.tracking') }}"
                                    class="underline font-medium hover:text-blue-900 dark:hover:text-blue-100">Tracking
                                    Status</a></li>
                            <li>Link download berlaku selama 7 hari setelah disetujui</li>
                            <li>Data yang diunduh hanya untuk keperluan yang telah disebutkan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
