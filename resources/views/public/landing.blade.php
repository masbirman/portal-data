@extends('public.layout')

@section('title', 'Beranda - Portal Data AN-TKA Disdik Sulteng')

@section('content')
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-slate-900 rounded-2xl shadow-2xl px-8 py-12 text-white mb-12">
        <!-- Background Pattern -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-slate-800 opacity-50 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-72 h-72 rounded-full bg-slate-800 opacity-50 blur-3xl"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <!-- Left Content -->
            <div class="lg:col-span-7">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-800 border border-slate-700 text-amber-400 text-xs font-medium mb-6">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                    Portal Data
                </div>

                <h1 class="text-4xl lg:text-5xl font-bold mb-4 leading-tight tracking-tight">
                    Asesmen Skala Nasional <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-200 to-amber-500">Disdik Prov.
                        Sulteng</span>
                </h1>

                <p class="text-slate-300 text-lg mb-8 leading-relaxed max-w-xl">
                    Menyajikan informasi statistik komprehensif pelaksanaan Asesmen Nasional (ANBK) dan Tes Kemampuan
                    Akademik (TKA) secara transparan dan akurat.
                </p>

                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('public.dashboard') }}"
                        class="group bg-amber-500 text-slate-900 px-6 py-3 rounded-lg font-bold hover:bg-amber-400 transition-all duration-300 flex items-center gap-2 shadow-[0_0_20px_rgba(245,158,11,0.3)] hover:shadow-[0_0_30px_rgba(245,158,11,0.5)]">
                        Lihat Dashboard
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Right Content - Illustration -->
            <div class="lg:col-span-5 flex justify-center lg:justify-end">
                <div class="relative">
                    <!-- Glow effect behind image -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-blue-500/20 to-purple-500/20 blur-2xl rounded-full">
                    </div>
                    <img src="{{ asset('storage/illustration-hero.png') }}" alt="Ilustrasi Dashboard"
                        class="relative z-10 w-full max-w-md drop-shadow-2xl hover:scale-105 transition-transform duration-500">
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    @livewire('public.stats-overview')

    <!-- Trend Chart -->
    @livewire('public.trend-chart')

    <!-- Wilayah Trend Chart -->
    @livewire('public.wilayah-trend-chart')
@endsection
