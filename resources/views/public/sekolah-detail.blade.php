@extends('public.layout')

@php
    use App\Services\SchoolStatisticsService;
    $statisticsService = app(SchoolStatisticsService::class);
    $statistics = $statisticsService->getStatistics($sekolah);
    $assessmentHistory = $statisticsService->getAssessmentHistory($sekolah);
    $nearbySchools = $statisticsService->getNearbySchools($sekolah);
@endphp

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('public.landing') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Beranda
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ route('sekolah.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Sekolah</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Detail Sekolah</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- School Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="h-32 bg-gradient-to-br from-blue-500 to-blue-600"></div>
        <div class="p-6 -mt-16">
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-lg p-6">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white mb-2">{{ $sekolah->nama }}</h1>
                <p class="text-gray-500 dark:text-gray-400 mb-4">NPSN: {{ $sekolah->kode_sekolah }}</p>
                
                @if($sekolah->alamat)
                    <p class="text-gray-600 dark:text-gray-300 mb-4 flex items-start">
                        <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ $sekolah->alamat }}
                    </p>
                @endif
                
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                        {{ $sekolah->wilayah->nama ?? '-' }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full {{ $sekolah->status_sekolah === 'Negeri' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' }}">
                        {{ $sekolah->status_sekolah ?? '-' }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                        {{ $sekolah->jenjangPendidikan->nama ?? '-' }}
                    </span>
                </div>
            </div>
        </div>
    </div>


    <!-- Statistics Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Statistik Asesmen
        </h2>
        
        @if($statistics['total_asesmen'] > 0)
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-4">
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Total Peserta</p>
                    <p class="text-2xl font-bold text-blue-800 dark:text-blue-200">{{ number_format($statistics['total_peserta']) }}</p>
                </div>
                <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-4">
                    <p class="text-sm text-green-600 dark:text-green-400 font-medium">Rata-rata Partisipasi Literasi</p>
                    <p class="text-2xl font-bold text-green-800 dark:text-green-200">{{ number_format($statistics['avg_literasi'], 2) }}%</p>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/30 rounded-lg p-4">
                    <p class="text-sm text-purple-600 dark:text-purple-400 font-medium">Rata-rata Partisipasi Numerasi</p>
                    <p class="text-2xl font-bold text-purple-800 dark:text-purple-200">{{ number_format($statistics['avg_numerasi'], 2) }}%</p>
                </div>
            </div>
            
            <!-- Assessment History Table -->
            @if($assessmentHistory->count() > 0)
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Riwayat Asesmen</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Siklus</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jumlah Peserta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Partisipasi Literasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Partisipasi Numerasi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($assessmentHistory as $history)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $history['siklus'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ number_format($history['jumlah_peserta']) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ number_format($history['partisipasi_literasi'], 2) }}%</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ number_format($history['partisipasi_numerasi'], 2) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @else
            <div class="text-center py-8">
                <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-gray-500 dark:text-gray-400">Belum ada data asesmen</p>
            </div>
        @endif
    </div>


    <!-- Map Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Lokasi Sekolah
        </h2>
        
        @if($sekolah->latitude && $sekolah->longitude)
            <div id="map" class="h-80 rounded-lg mb-4"></div>
            <a href="https://www.google.com/maps?q={{ $sekolah->latitude }},{{ $sekolah->longitude }}" 
               target="_blank" 
               rel="noopener noreferrer"
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Buka di Google Maps
            </a>
        @else
            <div class="text-center py-8">
                <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="text-gray-500 dark:text-gray-400">Lokasi tidak tersedia</p>
            </div>
        @endif
    </div>

    <!-- Nearby Schools Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Sekolah Sekitar
        </h2>
        
        @if($nearbySchools->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($nearbySchools as $nearby)
                    <a href="{{ route('sekolah.show', $nearby) }}" 
                       class="block bg-gray-50 dark:bg-gray-700 rounded-lg p-4 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <h3 class="font-semibold text-gray-800 dark:text-white mb-1 line-clamp-1">{{ $nearby->nama }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">NPSN: {{ $nearby->kode_sekolah }}</p>
                        <div class="flex flex-wrap gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full {{ $nearby->status_sekolah === 'Negeri' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' }}">
                                {{ $nearby->status_sekolah ?? '-' }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                                {{ $nearby->jenjangPendidikan->nama ?? '-' }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <p class="text-gray-500 dark:text-gray-400">Tidak ada sekolah sekitar</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@if($sekolah->latitude && $sekolah->longitude)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('map').setView([{{ $sekolah->latitude }}, {{ $sekolah->longitude }}], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        L.marker([{{ $sekolah->latitude }}, {{ $sekolah->longitude }}])
            .addTo(map)
            .bindPopup('<strong>{{ $sekolah->nama }}</strong>')
            .openPopup();
    });
</script>
@endif
@endpush
