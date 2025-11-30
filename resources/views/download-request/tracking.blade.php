@extends('public.layout')

@section('title', 'Tracking Pengajuan Download - Portal Data AN-TKA Disdik Sulteng')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg p-8 mb-6 text-white">
            <h1 class="text-3xl font-bold mb-2">Tracking Pengajuan Unduh Data</h1>
            <p class="text-blue-100">
                Cek status pengajuan download data Anda dengan memasukkan email yang digunakan saat mengajukan.
            </p>
        </div>

        <!-- Search Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 mb-6">
            @if (session('error'))
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('download-request.tracking.check') }}" method="POST">
                @csrf
                <div class="flex gap-4">
                    <div class="flex-1">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            placeholder="Masukkan email Anda"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl whitespace-nowrap">
                            🔍 Cek Status
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Results -->
        @if(isset($requests) && $requests->isNotEmpty())
            <div class="space-y-4">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                    Ditemukan {{ $requests->count() }} Pengajuan
                </h2>

                @foreach($requests as $request)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                        <!-- Status Header -->
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between
                            {{ $request->status === 'approved' ? 'bg-green-50 dark:bg-green-900/20' : ($request->status === 'rejected' ? 'bg-red-50 dark:bg-red-900/20' : 'bg-yellow-50 dark:bg-yellow-900/20') }}">
                            <div class="flex items-center gap-3">
                                @if($request->status === 'approved')
                                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 dark:bg-green-900/40 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                @elseif($request->status === 'rejected')
                                    <div class="flex-shrink-0 w-10 h-10 bg-red-100 dark:bg-red-900/40 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </div>
                                @else
                                    <div class="flex-shrink-0 w-10 h-10 bg-yellow-100 dark:bg-yellow-900/40 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="text-lg font-bold
                                        {{ $request->status === 'approved' ? 'text-green-800 dark:text-green-200' : ($request->status === 'rejected' ? 'text-red-800 dark:text-red-200' : 'text-yellow-800 dark:text-yellow-200') }}">
                                        {{ $request->status === 'approved' ? 'Disetujui' : ($request->status === 'rejected' ? 'Ditolak' : 'Menunggu Review') }}
                                    </h3>
                                    <p class="text-sm
                                        {{ $request->status === 'approved' ? 'text-green-600 dark:text-green-300' : ($request->status === 'rejected' ? 'text-red-600 dark:text-red-300' : 'text-yellow-600 dark:text-yellow-300') }}">
                                        Diajukan {{ $request->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            
                            @if($request->status === 'approved' && $request->isTokenValid())
                                <a href="{{ route('download-request.download', $request->download_token) }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Download
                                </a>
                            @endif
                        </div>

                        <!-- Request Details -->
                        <div class="px-6 py-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Nama Pemohon</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $request->nama }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $request->email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Jenis Data</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        {{ ucfirst(str_replace('_', ' ', $request->data_type)) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Tahun</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $request->tahun }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Wilayah</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $request->wilayah->nama ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Jenjang</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $request->jenjangPendidikan->nama ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Instansi</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $request->instansi }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Pengajuan</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $request->created_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>

                            @if($request->status === 'approved')
                                <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                    <p class="text-sm text-green-800 dark:text-green-200">
                                        <strong>Link berlaku hingga:</strong> {{ $request->token_expires_at->format('d M Y, H:i') }} WIB
                                    </p>
                                    @if($request->downloaded_at)
                                        <p class="text-sm text-green-600 dark:text-green-300 mt-1">
                                            ✓ Diunduh pada {{ $request->downloaded_at->format('d M Y, H:i') }}
                                        </p>
                                    @endif
                                </div>
                            @elseif($request->status === 'rejected' && $request->admin_notes)
                                <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                    <p class="text-sm font-semibold text-red-800 dark:text-red-200 mb-1">Alasan Penolakan:</p>
                                    <p class="text-sm text-red-700 dark:text-red-300">{{ $request->admin_notes }}</p>
                                </div>
                            @elseif($request->status === 'pending')
                                <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                        ⏳ Pengajuan Anda sedang dalam proses review. Kami akan mengirimkan notifikasi melalui email setelah direview.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Info Box -->
        <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Informasi</h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Waktu review: 1-2 hari kerja</li>
                            <li>Link download berlaku 7 hari setelah disetujui</li>
                            <li>Jika ada pertanyaan, hubungi admin melalui email</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
