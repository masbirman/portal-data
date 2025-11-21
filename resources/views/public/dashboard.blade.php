@extends('public.layout')

@section('title', 'Dashboard - Portal Data AN-TKA Disdik Sulteng')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Dashboard Data Pelaksanaan AN-TKA</h1>
    </div>

    <!-- Stats Overview -->
    @livewire('public.stats-overview')

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        <!-- Participation Chart -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Partisipasi per Tahun</h3>
            @livewire('public.participation-chart')
        </div>

        <!-- Jenjang Distribution Chart -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Distribusi per Jenjang</h3>
            @livewire('public.jenjang-chart')
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-lg p-6 mt-8">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Data Pelaksanaan Asesmen</h3>
        @livewire('public.data-table')
    </div>
@endsection
