@extends('public.layout')

@section('title', 'Beranda - Portal Data AN-TKA Disdik Sulteng')

@section('content')
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-xl px-8 py-8 text-white mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-center">
            <!-- Left Content -->
            <div>
                <h1 class="text-4xl font-bold mb-3">Selamat Datang di <span class="inline">Portal Data</span></h1>
                <h2 class="text-2xl font-semibold mb-4">AN-TKA Disdik Sulteng</h2>
                <a href="{{ route('public.dashboard') }}"
                    class="bg-white text-blue-600 px-5 py-2.5 rounded-lg font-semibold hover:bg-gray-100 inline-block text-sm">
                    Lihat Dashboard →
                </a>
            </div>

            <!-- Right Content - Space for illustration -->
            <div class="hidden lg:flex justify-center items-center">
                <img src="{{ asset('storage/illustration-hero.png') }}" alt="Ilustrasi Dashboard" class="w-full max-w-xs">
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    @livewire('public.stats-overview')

    <!-- Features -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="text-blue-600 mb-4">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Visualisasi Data</h3>
            <p class="text-gray-600">Lihat data pelaksanaan AN-TKA dalam bentuk grafik dan chart yang mudah dipahami.</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="text-green-600 mb-4">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                    </path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Filter & Pencarian</h3>
            <p class="text-gray-600">Cari data berdasarkan tahun, jenjang pendidikan, dan wilayah dengan mudah.</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="text-purple-600 mb-4">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Export Data</h3>
            <p class="text-gray-600">Download data dalam format Excel atau PDF untuk analisis lebih lanjut.</p>
        </div>
    </div>
@endsection
