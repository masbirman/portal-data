<div>
    <!-- Charts Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6" x-data="{
        stats: @js($chartStats),
        statusChart: null,
        modaChart: null,
    
        init() {
            this.renderCharts();
    
            this.$watch('stats', (value) => {
                this.renderCharts();
            });
        },
    
        renderCharts() {
            const commonOptions = {
                chart: {
                    type: 'bar',
                    height: 150,
                    toolbar: { show: false },
                    fontFamily: 'inherit',
                    parentHeightOffset: 0
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: true,
                        barHeight: '60%',
                        distributed: true,
                        dataLabels: {
                            position: 'top', // Value at the end of the bar
                            hideOverflowingLabels: false
                        }
                    }
                },
                dataLabels: {
                    enabled: false,
                    textAnchor: 'start',
                    style: {
                        colors: ['#374151'], // Dark text for values
                        fontSize: '12px',
                        fontWeight: 600
                    },
                    formatter: function(val, opt) {
                        return val; // Show only the number
                    },
                    offsetX: 10,
                    dropShadow: { enabled: false }
                },
                xaxis: {
                    labels: { show: false },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    min: 0,
                },
                yaxis: {
                    labels: {
                        show: true, // Show category names on Y-axis
                        style: {
                            fontSize: '12px',
                            fontFamily: 'inherit',
                            fontWeight: 500,
                            colors: ['#374151']
                        },
                        maxWidth: 120
                    }
                },
                grid: {
                    padding: { top: -10, right: 40, bottom: -10, left: 10 }, // Adjust padding
                    xaxis: { lines: { show: false } },
                    yaxis: { lines: { show: false } },
                },
                legend: { show: false },
                tooltip: {
                    theme: 'light',
                    y: {
                        formatter: function(val) {
                            return val + ' Sekolah'
                        }
                    }
                }
            };
    
            // Status Chart
            const statusOptions = {
                ...commonOptions,
                series: [{
                    name: 'Jumlah',
                    data: Object.values(this.stats.status)
                }],
                colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'],
                xaxis: {
                    ...commonOptions.xaxis,
                    categories: Object.keys(this.stats.status),
                }
            };
    
            if (this.statusChart) {
                this.statusChart.destroy();
            }
            this.statusChart = new ApexCharts(this.$refs.statusChart, statusOptions);
            this.statusChart.render();
    
            // Moda Chart
            const modaOptions = {
                ...commonOptions,
                series: [{
                    name: 'Jumlah',
                    data: Object.values(this.stats.moda)
                }],
                colors: ['#0ea5e9', '#f59e0b', '#8b5cf6', '#ec4899'],
                xaxis: {
                    ...commonOptions.xaxis,
                    categories: Object.keys(this.stats.moda),
                }
            };
    
            if (this.modaChart) {
                this.modaChart.destroy();
            }
            this.modaChart = new ApexCharts(this.$refs.modaChart, modaOptions);
            this.modaChart.render();
        }
    }">
        <!-- Status Chart Card -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-2 mb-2">
                <div class="p-1.5 bg-blue-50 rounded-lg">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-800">Status Pelaksanaan</h3>
            </div>
            <div x-ref="statusChart" class="-ml-2"></div>
        </div>

        <!-- Moda Chart Card -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-2 mb-2">
                <div class="p-1.5 bg-orange-50 rounded-lg">
                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-800">Moda Pelaksanaan</h3>
            </div>
            <div x-ref="modaChart" class="-ml-2"></div>
        </div>
    </div>
    <!-- Tabs Filter Jenjang -->
    <div class="mb-6">
        <div class="grid grid-cols-6 lg:grid-cols-11 gap-2">
            <button type="button" wire:click="setJenjangFilter('all')"
                class="px-3 py-1.5 text-sm rounded-lg font-semibold transition-colors duration-150
                    {{ $jenjangIdFilter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                SEMUA
            </button>
            @foreach ($jenjangList as $jenjang)
                <button type="button" wire:click="setJenjangFilter({{ $jenjang->id }})"
                    wire:key="jenjang-{{ $jenjang->id }}"
                    class="px-3 py-1.5 text-sm rounded-lg font-semibold transition-colors duration-150
                        {{ $jenjangIdFilter === $jenjang->id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    {{ strtoupper($jenjang->nama) }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Search Bar -->
    <div class="mb-4 flex flex-col md:flex-row gap-4 justify-between items-center">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama sekolah..."
            class="w-full md:w-1/2 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">

        <a href="{{ route('download-request.index') }}"
            class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            <span>Ajukan Unduh Data</span>
        </a>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Nama Sekolah</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider">Kota/Kabupaten</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider">Status Sekolah</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider">Peserta</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider">Status Pelaksanaan</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider">Moda Pelaksanaan</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider">Partisipasi Literasi
                        (%)</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider">Partisipasi Numerasi
                        (%)</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider">Tempat Pelaksanaan</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider">Penanggung Jawab</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider">Proktor</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider">Keterangan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($data as $item)
                    @php
                        $asesmen = $item->pelaksanaanAsesmen->first();
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <!-- Nama Sekolah -->
                        <td class="px-4 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $item->nama }}</div>
                            <div class="text-xs text-gray-500">{{ $item->jenjangPendidikan->nama }} -
                                {{ $item->kode_sekolah }}</div>
                        </td>

                        <!-- Kota/Kabupaten -->
                        <td class="px-4 py-4 text-center text-sm text-gray-700">
                            {{ $item->wilayah->nama }}
                        </td>

                        <!-- Status Sekolah -->
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            @if ($item->status_sekolah)
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $item->status_sekolah === 'Negeri' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $item->status_sekolah }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>

                        <!-- Jumlah Peserta -->
                        <td class="px-4 py-4 whitespace-nowrap text-center text-sm text-gray-900 font-semibold">
                            {{ $asesmen->jumlah_peserta ?? '-' }}
                        </td>

                        <!-- Status Pelaksanaan -->
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            @if ($asesmen)
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $asesmen->status_pelaksanaan === 'Mandiri' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $asesmen->status_pelaksanaan }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>

                        <!-- Moda Pelaksanaan -->
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            @if ($asesmen)
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $asesmen->moda_pelaksanaan === 'Online' ? 'bg-teal-100 text-teal-800' : 'bg-indigo-100 text-indigo-800' }}">
                                    {{ $asesmen->moda_pelaksanaan }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>

                        <!-- Partisipasi Literasi -->
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            @if ($asesmen)
                                <span
                                    class="text-sm font-semibold
                                    {{ $asesmen->partisipasi_literasi >= 100 ? 'text-green-600' : ($asesmen->partisipasi_literasi >= 80 ? 'text-blue-600' : 'text-orange-600') }}">
                                    {{ (float) $asesmen->partisipasi_literasi == 0 ? '0' : ($asesmen->partisipasi_literasi == 100 ? '100' : number_format($asesmen->partisipasi_literasi, 2)) }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>

                        <!-- Partisipasi Numerasi -->
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            @if ($asesmen)
                                <span
                                    class="text-sm font-semibold
                                    {{ $asesmen->partisipasi_numerasi >= 100 ? 'text-green-600' : ($asesmen->partisipasi_numerasi >= 80 ? 'text-blue-600' : 'text-orange-600') }}">
                                    {{ (float) $asesmen->partisipasi_numerasi == 0 ? '0' : ($asesmen->partisipasi_numerasi == 100 ? '100' : number_format($asesmen->partisipasi_numerasi, 2)) }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>

                        <!-- Tempat Pelaksanaan -->
                        <td class="px-4 py-4 text-sm text-gray-700">
                            {{ $asesmen->tempat_pelaksanaan ?? '-' }}
                        </td>

                        <!-- Nama Penanggung Jawab -->
                        <td class="px-4 py-4 text-sm text-gray-700">
                            {{ $asesmen->nama_penanggung_jawab ?? '-' }}
                        </td>

                        <!-- Nama Proktor -->
                        <td class="px-4 py-4 text-sm text-gray-700">
                            {{ $asesmen->nama_proktor ?? '-' }}
                        </td>

                        <!-- Keterangan -->
                        <td class="px-4 py-4 text-center text-sm text-gray-600">
                            {{ $asesmen->keterangan ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="px-6 py-8 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-2">Tidak ada data ditemukan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <span>Menampilkan {{ $data->firstItem() ?? 0 }} sampai {{ $data->lastItem() ?? 0 }} dari
                {{ $data->total() }} hasil</span>
        </div>

        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">per halaman</span>
                <select wire:model.live="perPage"
                    class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>

            @if ($data->hasPages())
                <div class="flex items-center gap-1">
                    {{-- Previous --}}
                    @if ($data->onFirstPage())
                        <span
                            class="px-3 py-1.5 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">&laquo;</span>
                    @else
                        <button wire:click="previousPage"
                            class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">&laquo;</button>
                    @endif

                    {{-- Page Numbers --}}
                    @php
                        $currentPage = $data->currentPage();
                        $lastPage = $data->lastPage();
                        $start = max(1, $currentPage - 2);
                        $end = min($lastPage, $currentPage + 2);
                    @endphp

                    @if ($start > 1)
                        <button wire:click="gotoPage(1)"
                            class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">1</button>
                        @if ($start > 2)
                            <span class="px-2 text-gray-500">...</span>
                        @endif
                    @endif

                    @for ($page = $start; $page <= $end; $page++)
                        @if ($page == $currentPage)
                            <span
                                class="px-3 py-1.5 text-sm text-white bg-blue-600 rounded-lg">{{ $page }}</span>
                        @else
                            <button wire:click="gotoPage({{ $page }})"
                                class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">{{ $page }}</button>
                        @endif
                    @endfor

                    @if ($end < $lastPage)
                        @if ($end < $lastPage - 1)
                            <span class="px-2 text-gray-500">...</span>
                        @endif
                        <button wire:click="gotoPage({{ $lastPage }})"
                            class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">{{ $lastPage }}</button>
                    @endif

                    {{-- Next --}}
                    @if ($data->hasMorePages())
                        <button wire:click="nextPage"
                            class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">&raquo;</button>
                    @else
                        <span
                            class="px-3 py-1.5 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">&raquo;</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="fixed top-0 left-0 right-0 bg-blue-500 h-1 z-50">
        <div class="h-full bg-blue-600 animate-pulse"></div>
    </div>
</div>
