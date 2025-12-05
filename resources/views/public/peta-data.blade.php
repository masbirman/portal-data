@extends('public.layout')

@section('title', 'Peta Data Asesmen ' . $tahun . ' - Portal Data AN-TKA Disdik Sulteng')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Peta Data Asesmen Nasional {{ $tahun }}</h1>
        <p class="text-gray-600 dark:text-gray-400">Visualisasi data pelaksanaan asesmen per kabupaten/kota di Sulawesi
            Tengah</p>
    </div>

    {{-- Filter Tahun --}}
    <div class="mb-6 flex items-center space-x-4">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tahun:</label>
        <select id="tahunFilter" onchange="window.location.href='/asesmen-nasional/' + this.value + '/peta'"
            class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @foreach ($availableYears as $year)
                <option value="{{ $year }}" {{ $year == $tahun ? 'selected' : '' }}>{{ $year }}</option>
            @endforeach
        </select>
    </div>

    {{-- Peta Container --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div id="map" class="w-full"></div>
    </div>

    {{-- Legenda --}}
    <div class="mt-4 bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Keterangan Peta</h3>

        {{-- Pin Marker Legend --}}
        <div class="mb-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Penanda Lokasi:</p>
            <div class="flex flex-wrap gap-3">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-1.5" viewBox="0 0 24 24" fill="#3b82f6">
                        <path
                            d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                    </svg>
                    <span class="text-xs text-gray-600 dark:text-gray-400">Lokasi Kabupaten/Kota</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full mr-1.5 animate-ping"
                        style="background-color: #3b82f6; animation-duration: 2s;"></div>
                    <span class="text-xs text-gray-600 dark:text-gray-400">Efek kedip menandakan lokasi aktif</span>
                </div>
            </div>
        </div>

        {{-- Interaction Hints --}}
        <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Cara Interaksi:</p>
            <div class="flex flex-wrap gap-x-4 gap-y-2 text-xs text-gray-600 dark:text-gray-400">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122">
                        </path>
                    </svg>
                    <span>Arahkan kursor ke pin untuk melihat ringkasan data</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                    <span>Klik pin untuk melihat detail lengkap dan navigasi ke halaman wilayah</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Ringkasan --}}
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Ringkasan Data Per Wilayah</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-indigo-900 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Wilayah</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider">Sekolah</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider">Peserta</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider" colspan="2">Status
                            Pelaksanaan</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider" colspan="2">Moda
                            Pelaksanaan</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider">Aksi</th>
                    </tr>
                    <tr class="bg-indigo-800 text-white text-xs">
                        <th class="px-4 py-2"></th>
                        <th class="px-4 py-2"></th>
                        <th class="px-4 py-2"></th>
                        <th class="px-3 py-2 text-center font-normal">Mandiri</th>
                        <th class="px-3 py-2 text-center font-normal">Menumpang</th>
                        <th class="px-3 py-2 text-center font-normal">Online</th>
                        <th class="px-3 py-2 text-center font-normal">Semi Online</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($wilayahData as $wilayah)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if ($wilayah['logo'])
                                        <img src="{{ asset('storage/' . $wilayah['logo']) }}" alt="{{ $wilayah['nama'] }}"
                                            class="w-8 h-8 rounded-full mr-3 object-contain bg-gray-100">
                                    @else
                                        <div
                                            class="w-8 h-8 rounded-full mr-3 bg-indigo-100 flex items-center justify-center">
                                            <span
                                                class="text-indigo-600 font-bold text-xs">{{ substr($wilayah['nama'], 0, 2) }}</span>
                                        </div>
                                    @endif
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $wilayah['nama'] }}</span>
                                </div>
                            </td>
                            <td
                                class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 text-center font-medium">
                                {{ number_format($wilayah['total_sekolah']) }}
                            </td>
                            <td
                                class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 text-center font-medium">
                                {{ number_format($wilayah['total_peserta']) }}
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                    {{ $wilayah['status_mandiri'] }}
                                </span>
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                    {{ $wilayah['status_menumpang'] }}
                                </span>
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                                    {{ $wilayah['moda_online'] }}
                                </span>
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">
                                    {{ $wilayah['moda_semi_online'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                <a href="{{ route('asesmen-nasional.wilayah', ['tahun' => $tahun, 'wilayah' => $wilayah['id']]) }}"
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                    Lihat Detail →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* Popup Styles */
        .leaflet-popup-content-wrapper {
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .leaflet-popup-content {
            margin: 12px;
        }

        .map-popup .leaflet-popup-content-wrapper {
            padding: 0;
        }

        /* Tooltip Styles */
        .map-tooltip {
            background-color: rgba(255, 255, 255, 0.95);
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 6px 10px;
            font-size: 12px;
        }

        .map-tooltip::before {
            border-top-color: rgba(255, 255, 255, 0.95);
        }

        /* Dark mode support for tooltips */
        .dark .map-tooltip {
            background-color: rgba(31, 41, 55, 0.95);
            border-color: #374151;
            color: #f3f4f6;
        }

        /* Polygon hover transition */
        .leaflet-interactive {
            transition: fill-opacity 0.2s ease;
        }

        /* ========================================
           Responsive Map Styles (Requirement 7.1, 7.3)
           ======================================== */

        /* Default map container height */
        #map {
            height: 500px;
            min-height: 300px;
        }

        /* Tablet viewport (768px and below) */
        @media (max-width: 768px) {
            #map {
                height: 400px;
            }

            /* Adjust popup width for tablets */
            .leaflet-popup-content-wrapper {
                max-width: 280px;
            }

            .leaflet-popup-content {
                margin: 10px;
                font-size: 13px;
            }
        }

        /* Mobile viewport (480px and below) */
        @media (max-width: 480px) {
            #map {
                height: 300px;
            }

            /* Smaller popup for mobile */
            .leaflet-popup-content-wrapper {
                max-width: 240px;
            }

            .leaflet-popup-content {
                margin: 8px;
                font-size: 12px;
            }

            /* Ensure popup close button is touch-friendly */
            .leaflet-popup-close-button {
                width: 24px;
                height: 24px;
                font-size: 20px;
                padding: 4px;
            }

            /* Smaller tooltip for mobile */
            .map-tooltip {
                font-size: 11px;
                padding: 4px 8px;
                max-width: 180px;
            }
        }

        /* Very small screens (360px and below) */
        @media (max-width: 360px) {
            #map {
                height: 250px;
            }

            .leaflet-popup-content-wrapper {
                max-width: 200px;
            }

            .leaflet-popup-content {
                margin: 6px;
                font-size: 11px;
            }

            .map-tooltip {
                font-size: 10px;
                padding: 3px 6px;
                max-width: 150px;
            }
        }

        /* Landscape orientation on mobile - use more horizontal space */
        @media (max-width: 768px) and (orientation: landscape) {
            #map {
                height: 280px;
            }
        }

        /* Ensure popup content text wraps properly on small screens */
        .leaflet-popup-content p,
        .leaflet-popup-content div {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Touch-friendly zoom controls on mobile */
        @media (max-width: 768px) {
            .leaflet-control-zoom a {
                width: 36px;
                height: 36px;
                line-height: 36px;
                font-size: 18px;
            }
        }

        /* Improve tooltip readability on all screen sizes */
        .map-tooltip {
            line-height: 1.4;
            white-space: normal;
            word-wrap: break-word;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/map-visualization.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data wilayah dari PHP
            const wilayahData = @json($wilayahData);
            const tahun = '{{ $tahun }}';
            const geojsonUrl = '{{ asset('geojson/sulawesi_tengah_72_batas_kabkota.geojson') }}';

            // Initialize map using map-visualization module (Requirement 1.1)
            const map = window.MapVisualization.initMap('map', {
                center: [-1.0, 121.0],
                zoom: 7
            });

            // Load GeoJSON and render polygons (Requirement 1.2)
            window.MapVisualization.loadGeoJSON(map, geojsonUrl, wilayahData, tahun)
                .then(function(geojsonLayer) {
                    if (geojsonLayer) {
                        console.log('GeoJSON loaded successfully with polygon visualization');
                    } else {
                        console.log('Using fallback circle markers');
                    }
                })
                .catch(function(error) {
                    console.error('Error initializing map:', error);
                });
        });
    </script>
@endpush
