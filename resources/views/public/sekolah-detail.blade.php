@extends('public.layout')

@php
    use App\Services\SchoolStatisticsService;
    $statisticsService = app(SchoolStatisticsService::class);
    $statistics = $statisticsService->getStatistics($sekolah);
    $assessmentHistory = $statisticsService->getAssessmentHistory($sekolah);
    $nearbySchools = $statisticsService->getNearbySchools($sekolah);
    $latestOfficials = $statisticsService->getLatestOfficials($sekolah);

    // Helper function to format percentage without unnecessary decimals
    $formatPercent = function ($value) {
        return $value == floor($value) ? number_format($value, 0) : number_format($value, 2);
    };
@endphp

@section('content')
    <div class="space-y-6">
        <!-- Breadcrumb -->
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('public.landing') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                            </path>
                        </svg>
                        Beranda
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('direktori-sekolah.index') }}"
                            class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Data
                            Sekolah</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Detail
                            Sekolah</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- School Header - 3 Column Layout -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-0">
                <!-- Left: Image/Illustration -->
                <div class="lg:col-span-1 min-h-[280px] relative overflow-hidden">
                    @if ($sekolah->foto)
                        <!-- School Photo -->
                        <img src="{{ asset('storage/' . $sekolah->foto) }}" alt="Foto {{ $sekolah->nama }}"
                            class="w-full h-full object-cover absolute inset-0">
                    @else
                        <!-- Illustration Placeholder -->
                        <img src="{{ asset('images/school-illustration.svg') }}" alt="Ilustrasi Sekolah"
                            class="w-full h-full object-cover absolute inset-0">
                    @endif
                </div>

                <!-- Right: School Info -->
                <div class="lg:col-span-2 p-6">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white mb-2">{{ $sekolah->nama }}</h1>

                    @if ($sekolah->alamat)
                        <p class="text-gray-600 dark:text-gray-300 mb-4 flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $sekolah->alamat }}
                        </p>
                    @endif

                    <!-- Info Grid -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">NPSN</p>
                                <p class="font-semibold text-gray-800 dark:text-white">{{ $sekolah->npsn ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Kode Sekolah</p>
                                <p class="font-semibold text-gray-800 dark:text-white">{{ $sekolah->kode_sekolah }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Jenjang</p>
                                <p class="font-semibold text-gray-800 dark:text-white">
                                    {{ $sekolah->jenjangPendidikan->nama ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Wilayah</p>
                                <p class="font-semibold text-gray-800 dark:text-white">{{ $sekolah->wilayah->nama ?? '-' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Status</p>
                                <p class="font-semibold text-gray-800 dark:text-white">
                                    {{ $sekolah->status_sekolah ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Tags -->
                    <div class="flex flex-wrap gap-2">
                        <span
                            class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                            {{ $sekolah->wilayah->nama ?? '-' }}
                        </span>
                        <span
                            class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full {{ $sekolah->status_sekolah === 'Negeri' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' }}">
                            {{ $sekolah->status_sekolah ?? '-' }}
                        </span>
                        <span
                            class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                            {{ $sekolah->jenjangPendidikan->nama ?? '-' }}
                        </span>
                    </div>
                </div>

                <!-- Wilayah Logo & Officials -->
                @if ($sekolah->wilayah)
                    <div
                        class="hidden lg:flex flex-col items-center justify-center p-6 border-l border-gray-100 dark:border-gray-700">
                        <div class="text-center mb-4">
                            @if ($sekolah->wilayah->logo)
                                <img src="{{ asset('storage/' . $sekolah->wilayah->logo) }}"
                                    alt="Logo {{ $sekolah->wilayah->nama }}"
                                    class="w-20 h-20 object-contain mx-auto mb-2">
                            @endif
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ $sekolah->wilayah->nama }}
                            </p>
                        </div>

                        <!-- Penanggung Jawab & Proktor -->
                        @if ($latestOfficials['nama_penanggung_jawab'] || $latestOfficials['nama_proktor'])
                            <div class="w-full space-y-3 mb-4">
                                @if ($latestOfficials['nama_penanggung_jawab'])
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Penanggung Jawab</p>
                                        <p class="font-semibold text-gray-800 dark:text-white text-sm">
                                            {{ $latestOfficials['nama_penanggung_jawab'] }}</p>
                                    </div>
                                @endif
                                @if ($latestOfficials['nama_proktor'])
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Proktor</p>
                                        <p class="font-semibold text-gray-800 dark:text-white text-sm">
                                            {{ $latestOfficials['nama_proktor'] }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Link to Wilayah Statistics -->
                        <a href="{{ route('asesmen-nasional.wilayah', ['tahun' => 2024, 'wilayah' => $sekolah->wilayah]) }}"
                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Lihat Data Statistik
                        </a>
                    </div>
                @endif
            </div>
        </div>


        <!-- Two Column Section: Statistics & Map/Nearby -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Statistics -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Statistics Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Statistik Asesmen
                    </h2>

                    @if ($statistics['total_asesmen'] > 0)
                        <!-- Stats Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-4">
                                <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Total Peserta</p>
                                <p class="text-2xl font-bold text-blue-800 dark:text-blue-200">
                                    {{ number_format($statistics['total_peserta']) }}</p>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-4">
                                <p class="text-sm text-green-600 dark:text-green-400 font-medium">Rata-rata Literasi</p>
                                <p class="text-2xl font-bold text-green-800 dark:text-green-200">
                                    {{ $formatPercent($statistics['avg_literasi']) }}%</p>
                            </div>
                            <div class="bg-purple-50 dark:bg-purple-900/30 rounded-lg p-4">
                                <p class="text-sm text-purple-600 dark:text-purple-400 font-medium">Rata-rata Numerasi</p>
                                <p class="text-2xl font-bold text-purple-800 dark:text-purple-200">
                                    {{ $formatPercent($statistics['avg_numerasi']) }}%</p>
                            </div>
                        </div>

                        <!-- Assessment History Table -->
                        @if ($assessmentHistory->count() > 0)
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Riwayat Asesmen</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                Siklus</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                Peserta</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                Literasi</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                Numerasi</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                Penanggung Jawab</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                Proktor</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($assessmentHistory as $history)
                                            <tr>
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                                    {{ $history['siklus'] }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ number_format($history['jumlah_peserta']) }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $formatPercent($history['partisipasi_literasi']) }}%</td>
                                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $formatPercent($history['partisipasi_numerasi']) }}%</td>
                                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $history['nama_penanggung_jawab'] && $history['nama_penanggung_jawab'] !== '-' ? $history['nama_penanggung_jawab'] : '-' }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $history['nama_proktor'] && $history['nama_proktor'] !== '-' ? $history['nama_proktor'] : '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">Belum ada data asesmen</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Map & Nearby Schools -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Map Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Lokasi
                    </h2>

                    @if ($sekolah->latitude && $sekolah->longitude)
                        <div id="map" class="h-48 rounded-lg mb-3"></div>
                        <div class="flex gap-2">
                            <a href="https://www.google.com/maps?q={{ $sekolah->latitude }},{{ $sekolah->longitude }}"
                                target="_blank" rel="noopener noreferrer"
                                class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                Google Maps
                            </a>
                        </div>
                    @else
                        <div class="text-center py-6">
                            <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-2" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Lokasi tidak tersedia</p>
                        </div>
                    @endif
                </div>

                <!-- Nearby Schools Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Sekolah Sekitar
                    </h2>

                    @if ($nearbySchools->count() > 0)
                        <div class="space-y-3">
                            @foreach ($nearbySchools as $nearby)
                                <a href="{{ route('direktori-sekolah.show', $nearby) }}"
                                    class="block bg-gray-50 dark:bg-gray-700 rounded-lg p-3 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                    <h3 class="font-semibold text-gray-800 dark:text-white text-sm line-clamp-1">
                                        {{ $nearby->nama }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $nearby->kode_sekolah }}
                                    </p>
                                    <div class="flex flex-wrap gap-1">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full {{ $nearby->status_sekolah === 'Negeri' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' }}">
                                            {{ $nearby->status_sekolah ?? '-' }}
                                        </span>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                                            {{ $nearby->jenjangPendidikan->nama ?? '-' }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6">
                            <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-2" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Tidak ada sekolah sekitar</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @if ($sekolah->latitude && $sekolah->longitude)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const map = L.map('map').setView([{{ $sekolah->latitude }}, {{ $sekolah->longitude }}], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OSM'
                }).addTo(map);

                L.marker([{{ $sekolah->latitude }}, {{ $sekolah->longitude }}])
                    .addTo(map)
                    .bindPopup('<strong>{{ $sekolah->nama }}</strong>')
                    .openPopup();
            });
        </script>
    @endif
@endpush
