<div class="mt-8 bg-white rounded-lg shadow-lg p-6">
    <div class="mb-6">
        <h3 class="text-xl font-bold text-gray-800">Statistik Per Wilayah & Jenjang</h3>
        <p class="text-gray-600 text-sm">Filter data asesmen berdasarkan tahun, wilayah, dan jenjang pendidikan.</p>
    </div>

    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Tahun</label>
            <select wire:model.live="selectedTahun"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                @foreach ($years as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Wilayah</label>
            <select wire:model.live="selectedWilayah"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                <option value="">-- Pilih Kota/Kabupaten --</option>
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

    @if ($chartData)
        <div class="mb-4 p-3 bg-slate-100 rounded-lg">
            <p class="text-sm text-gray-700">
                <span class="font-semibold">Tahun {{ $selectedTahun }}</span> |
                <span class="text-slate-600">{{ $selectedWilayahName }}</span> |
                <span class="text-slate-600">{{ $selectedJenjangName }}</span>
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-slate-50 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-600">Jumlah Sekolah</p>
                <p class="text-3xl font-bold text-slate-900">{{ number_format($chartData['jumlah_sekolah']) }}</p>
            </div>
            <div class="bg-slate-50 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-600">Jumlah Peserta</p>
                <p class="text-3xl font-bold text-slate-900">{{ number_format($chartData['jumlah_peserta']) }}</p>
            </div>
            <div class="bg-slate-50 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-600">Rata-rata Keikutsertaan</p>
                <p class="text-3xl font-bold text-amber-500">{{ $chartData['keikutsertaan'] }}%</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Status Pelaksanaan</h4>
                <div x-data="statusChart(@js($chartData['status']))" x-init="initChart"
                    wire:key="status-{{ $selectedTahun }}-{{ $selectedWilayah }}-{{ $selectedJenjang }}">
                    <div x-ref="chart" class="h-[200px]"></div>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Moda Pelaksanaan</h4>
                <div x-data="modaChart(@js($chartData['moda']))" x-init="initChart"
                    wire:key="moda-{{ $selectedTahun }}-{{ $selectedWilayah }}-{{ $selectedJenjang }}">
                    <div x-ref="chart" class="h-[200px]"></div>
                </div>
            </div>
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-12 text-gray-500">
            <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                </path>
            </svg>
            <p>Pilih wilayah dan jenjang untuk melihat statistik</p>
        </div>
    @endif

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('statusChart', (data) => ({
                chart: null,
                initChart() {
                    if (this.chart) this.chart.destroy();

                    const labels = Object.keys(data);
                    const values = Object.values(data);

                    let options = {
                        series: values,
                        labels: labels,
                        chart: {
                            type: 'donut',
                            height: 200
                        },
                        colors: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
                        legend: {
                            position: 'bottom'
                        },
                        dataLabels: {
                            enabled: true
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '60%'
                                }
                            }
                        }
                    };

                    this.chart = new ApexCharts(this.$refs.chart, options);
                    this.chart.render();
                }
            }));

            Alpine.data('modaChart', (data) => ({
                chart: null,
                initChart() {
                    if (this.chart) this.chart.destroy();

                    const labels = Object.keys(data);
                    const values = Object.values(data);

                    let options = {
                        series: values,
                        labels: labels,
                        chart: {
                            type: 'donut',
                            height: 200
                        },
                        colors: ['#0ea5e9', '#f59e0b', '#8b5cf6', '#ec4899'],
                        legend: {
                            position: 'bottom'
                        },
                        dataLabels: {
                            enabled: true
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '60%'
                                }
                            }
                        }
                    };

                    this.chart = new ApexCharts(this.$refs.chart, options);
                    this.chart.render();
                }
            }));
        });
    </script>
</div>
