@extends('public.layout')

@section('title', 'Permintaan Terkirim - Portal Data AN-TKA Disdik Sulteng')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 text-center">
            <!-- Success Icon -->
            <div
                class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900/20 mb-6">
                <svg class="h-10 w-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <!-- Success Message -->
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                Permintaan Berhasil Dikirim!
            </h1>

            <p class="text-gray-600 dark:text-gray-300 mb-8">
                Terima kasih atas permintaan download data Anda. Tim kami akan meninjau permintaan Anda dalam waktu 1-2 hari
                kerja.
            </p>

            <!-- Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <div class="text-blue-600 dark:text-blue-400 font-semibold mb-1">Waktu Review</div>
                    <div class="text-gray-700 dark:text-gray-300">1-2 Hari Kerja</div>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <div class="text-blue-600 dark:text-blue-400 font-semibold mb-1">Masa Berlaku Link</div>
                    <div class="text-gray-700 dark:text-gray-300">7 Hari</div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6 mb-8 text-left">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Langkah Selanjutnya:</h2>
                <ol class="space-y-3 text-gray-600 dark:text-gray-300">
                    <li class="flex items-start">
                        <span
                            class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm mr-3">1</span>
                        <span>Cek status pengajuan Anda secara berkala melalui menu <strong>Tracking Status</strong></span>
                    </li>
                    <li class="flex items-start">
                        <span
                            class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm mr-3">2</span>
                        <span>Jika disetujui, tombol download akan tersedia di halaman tracking</span>
                    </li>
                    <li class="flex items-start">
                        <span
                            class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm mr-3">3</span>
                        <span>Klik tombol download untuk mengunduh data yang diminta</span>
                    </li>
                    <li class="flex items-start">
                        <span
                            class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm mr-3">4</span>
                        <span>Gunakan data sesuai dengan tujuan yang telah disebutkan</span>
                    </li>
                </ol>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('public.landing') }}"
                    class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    ← Kembali ke Beranda
                </a>
                <a href="{{ route('download-request.tracking') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    🔍 Cek Status Pengajuan
                </a>
            </div>
        </div>
    </div>
@endsection
