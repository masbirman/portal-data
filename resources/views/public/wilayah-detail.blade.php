@extends('public.layout')

@section('title', $wilayah->nama . ' - Asesmen Nasional ' . $tahun . ' - Portal Data AN-TKA Disdik Sulteng')

@section('content')
    <!-- Breadcrumb -->
    <nav class="mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li>
                <a href="{{ route('public.landing') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </li>
            <li>
                <a href="{{ route('asesmen-nasional.index', ['tahun' => $tahun]) }}" class="hover:text-blue-600 transition-colors">
                    Asesmen Nasional {{ $tahun }}
                </a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </li>
            <li class="text-gray-900 font-medium">{{ $wilayah->nama }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-4 mb-4">
            @if($wilayah->logo_path)
                <img src="{{ Storage::url($wilayah->logo_path) }}" 
                     alt="{{ $wilayah->nama }}" 
                     class="h-16 w-16 rounded-full object-cover shadow-lg">
            @else
                <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center shadow-lg">
                    <span class="text-blue-600 font-bold text-2xl">
                        {{ substr($wilayah->nama, 0, 2) }}
                    </span>
                </div>
            @endif
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $wilayah->nama }}</h1>
                <p class="text-gray-600">Jumlah Satuan Pendidikan Pelaksanaan Asesmen Nasional Tahun {{ $tahun }}</p>
            </div>
        </div>
    </div>

    <!-- Stats Header for Wilayah -->
    @livewire('public.asesmen-stats-header', ['tahun' => $tahun, 'wilayahId' => $wilayah->id])

    <!-- Section Title -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Data Detail Sekolah</h2>
        <p class="text-gray-600">Filter data berdasarkan jenjang pendidikan</p>
    </div>

    <!-- Detail Table -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        @livewire('public.wilayah-detail-table', ['tahun' => $tahun, 'wilayahId' => $wilayah->id])
    </div>

    <!-- Back Button -->
    <div class="mt-8">
        <a href="{{ route('asesmen-nasional.index', ['tahun' => $tahun]) }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors duration-150">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Data Agregat
        </a>
    </div>

    <!-- Footer Info -->
    <div class="mt-8 text-center text-sm text-gray-500">
        © {{ date('Y') }} | Sekretariat AN-TKA Disdik Sulteng
    </div>
@endsection

