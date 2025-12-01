<div class="mt-8 bg-white rounded-lg shadow-lg p-6">
    <div class="mb-6">
        <h3 class="text-xl font-bold text-gray-800">Tren Per Wilayah</h3>
        <p class="text-gray-600 text-sm">Perkembangan data asesmen per kota/kabupaten dari tahun ke tahun.</p>
    </div>

    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Wilayah</label>
            <select wire:model.live="selectedWilayah"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                <option value="all">Semua Kota/Kabupaten</option>
                @foreach ($wilayahs as $wilayah)
                    <option value="{{ $wilayah->id }}">{{ $wilayah->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Jenjang</label>
            <select wire:model.live="selectedJenjang"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                <option value="">-- Pilih Jenjang --</option>
                <option value="all">Semua Jenjang</option>
                @foreach ($jenjangs as $jenjang)
                    <option value="{{ $jenjang->id }}">{{ $jenjang->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if ($chartData && $selectedJenjang)
        <div class="mb-4 p-3 bg-slate-100 rounded-lg">
            <p class="text-sm text-gray-700">
                <span class="font-semibold">{{ $selectedWilayahName }}</span> -
                <span class="text-slate-600">{{ $selectedJenjangName }}</span>
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            @php
                $latestIdx = count($chartData['categories']) - 1;
                $prevIdx = $latestIdx > 0 ? $latestIdx - 1 : 0;
            @endphp
            <div class="bg-slate-50 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-600">Jumlah Sekolah ({{ $chartData['categories'][$latestIdx] ?? '-' }})</p>
                <p class="text-2xl font-bold text-slate-900">
                    {{ number_format($chartData['series'][0]['data'][$latestIdx] ?? 0) }}</p>
                @if ($latestIdx > 0)
                    @php
                        $diff =
                            ($chartData['series'][0]['data'][$latestIdx] ?? 0) -
                            ($chartData['series'][0]['data'][$prevIdx] ?? 0);
                    @endphp
                    <p class="text-xs {{ $diff >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $diff >= 0 ? '+' : '' }}{{ $diff }} dari tahun sebelumnya
                    </p>
                @endif
            </div>
            <div class="bg-slate-50 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-600">Jumlah Peserta ({{ $chartData['categories'][$latestIdx] ?? '-' }})</p>
                <p class="text-2xl font-bold text-slate-900">
                    {{ number_format($chartData['series'][1]['data'][$latestIdx] ?? 0) }}</p>
                @if ($latestIdx > 0)
                    @php
                        $diff =
                            ($chartData['series'][1]['data'][$latestIdx] ?? 0) -
                            ($chartData['series'][1]['data'][$prevIdx] ?? 0);
                    @endphp
                    <p class="text-xs {{ $diff >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $diff >= 0 ? '+' : '' }}{{ number_format($diff) }} dari tahun sebelumnya
                    </p>
                @endif
            </div>
            <div class="bg-slate-50 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-600">Keikutsertaan ({{ $chartData['categories'][$latestIdx] ?? '-' }})</p>
                <p class="text-2xl font-bold text-amber-500">{{ $chartData['series'][2]['data'][$latestIdx] ?? 0 }}%
                </p>
                @if ($latestIdx > 0)
                    @php
                        $diff =
                            ($chartData['series'][2]['data'][$latestIdx] ?? 0) -
                            ($chartData['series'][2]['data'][$prevIdx] ?? 0);
                    @endphp
                    <p class="text-xs {{ $diff >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $diff >= 0 ? '+' : '' }}{{ $diff }}% dari tahun sebelumnya
                    </p>
                @endif
            </div>
        </div>

        <div x-data="wilayahTrendChart(@js($chartData))" x-init="initChart" class="w-full h-[350px]"
            wire:key="chart-{{ $selectedWilayah }}-{{ $selectedJenjang }}">
            <div x-ref="chart"></div>
        </div>
    @elseif (!$selectedJenjang)
        <div class="flex flex-col items-center justify-center py-12 text-gray-500">
            <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                </path>
            </svg>
            <p>Pilih jenjang untuk melihat grafik trend</p>
        </div>
    @endif

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('wilayahTrendChart', (data) => ({
                chart: null,
                initChart() {
                    if (this.chart) {
                        this.chart.destroy();
                    }

                    let options = {
                        series: [{
                            name: 'Jumlah Sekolah',
                            type: 'column',
                            data: data.series[0].data
                        }, {
                            name: 'Jumlah Peserta',
                            type: 'line',
                            data: data.series[1].data
                        }, {
                            name: 'Keikutsertaan (%)',
                            type: 'line',
                            data: data.series[2].data
                        }],
                        chart: {
                            height: 350,
                            type: 'line',
                            toolbar: {
                                show: false
                            },
                            fontFamily: 'Instrument Sans, sans-serif'
                        },
                        colors: ['#cbd5e1', '#0f172a', '#f59e0b'],
                        stroke: {
                            width: [0, 3, 3],
                            curve: 'smooth'
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                columnWidth: '60%'
                            }
                        },
                        markers: {
                            size: [0, 5, 5],
                            colors: ['#cbd5e1', '#0f172a', '#f59e0b'],
                            strokeColors: '#fff',
                            strokeWidth: 2
                        },
                        xaxis: {
                            categories: data.categories,
                            title: {
                                text: 'Tahun',
                                style: {
                                    color: '#64748b'
                                }
                            }
                        },
                        yaxis: [{
                            title: {
                                text: 'Jumlah Sekolah',
                                style: {
                                    color: '#64748b'
                                }
                            },
                            labels: {
                                formatter: (val) => Math.round(val)
                            }
                        }, {
                            opposite: true,
                            title: {
                                text: 'Jumlah Peserta',
                                style: {
                                    color: '#0f172a'
                                }
                            },
                            labels: {
                                formatter: (val) => val.toLocaleString('id-ID')
                            }
                        }, {
                            opposite: true,
                            show: false,
                            max: 100
                        }],
                        legend: {
                            position: 'top',
                            horizontalAlign: 'right'
                        },
                        tooltip: {
                            shared: true,
                            intersect: false,
                            y: {
                                formatter: function(y, {
                                    seriesIndex
                                }) {
                                    if (seriesIndex === 2) return y + "%";
                                    return y.toLocaleString('id-ID');
                                }
                            }
                        },
                        grid: {
                            borderColor: '#f1f5f9',
                            strokeDashArray: 4
                        }
                    };

                    this.chart = new ApexCharts(this.$refs.chart, options);
                    this.chart.render();
                }
            }));
        });
    </script>
</div>
