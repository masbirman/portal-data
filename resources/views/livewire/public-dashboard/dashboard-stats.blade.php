<div>
    <h1 class="text-3xl font-bold text-center mb-2 text-gray-800">Statistik</h1>
    <p class="text-center text-gray-500 mb-8">Statistik Asesmen Nasional</p>

    {{-- Filters (Sticky) --}}
    <div class="sticky top-16 z-40 bg-white p-4 rounded-xl shadow-lg border border-gray-100 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <select wire:model.live="selectedYear"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @foreach ($this->filters['years'] as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
            <select wire:model.live="selectedWilayah"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Sulawesi Tengah</option>
                @foreach ($this->filters['wilayahs'] as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
            <select wire:model.live="selectedJenjang"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Semua Jenjang</option>
                @foreach ($this->filters['jenjangs'] as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex space-x-6 mb-6 border-b border-gray-200">
        <button wire:click="$set('activeTab', 'status')"
            class="pb-2 px-1 {{ $activeTab === 'status' ? 'border-b-2 border-blue-500 text-blue-600 font-medium' : 'text-gray-500 hover:text-gray-700' }}">
            Status Pelaksanaan
        </button>
        <button wire:click="$set('activeTab', 'moda')"
            class="pb-2 px-1 {{ $activeTab === 'moda' ? 'border-b-2 border-blue-500 text-blue-600 font-medium' : 'text-gray-500 hover:text-gray-700' }}">
            Moda Pelaksanaan
        </button>
    </div>

    {{-- Charts Title --}}
    <h2 class="text-center text-xl text-gray-700 mb-6">
        Persentase Satuan Pendidikan Pelaksana ANBK
    </h2>

    {{-- Charts Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-12">
        @foreach ($this->stats['charts'] as $jenjang => $data)
            @if (!$selectedJenjang || $selectedJenjang == \App\Models\JenjangPendidikan::where('nama', $jenjang)->first()->id)
                <div class="bg-white p-2 rounded-xl shadow-sm border border-gray-100 h-[160px] flex items-center">
                    {{-- Chart Section --}}
                    <div class="relative w-[60%] h-full flex items-center justify-center">
                        {{-- Centered Text --}}
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-10">
                            <span
                                class="text-[10px] font-bold text-gray-700 text-center leading-tight px-1">{{ $jenjang }}</span>
                        </div>

                        {{-- Chart --}}
                        <div class="w-full h-full flex items-center justify-center" x-data="donutChart(
                            @js($activeTab === 'status' ? $data['status'] : $data['moda']),
                            @js($activeTab === 'status' ? ['Mandiri', 'Menumpang'] : ['Online', 'Semi Online']),
                            @js($activeTab === 'status' ? ['#10B981', '#3B82F6'] : ['#3B82F6', '#F59E0B'])
                        )"
                            x-init="initChart"
                            wire:key="chart-{{ $jenjang }}-{{ $activeTab }}-{{ implode('-', $activeTab === 'status' ? $data['status'] : $data['moda']) }}">
                            <div x-ref="chart" class="w-full flex justify-center"></div>
                        </div>
                    </div>

                    {{-- Custom Legend --}}
                    <div class="w-[40%] pl-2 flex flex-col justify-center space-y-2">
                        @if ($activeTab === 'status')
                            <div class="flex items-center">
                                <span class="w-3 h-3 rounded-full bg-emerald-500 mr-2"></span>
                                <span class="text-[10px] text-gray-600 leading-tight">Mandiri</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-3 h-3 rounded-full bg-blue-500 mr-2"></span>
                                <span class="text-[10px] text-gray-600 leading-tight">Menumpang</span>
                            </div>
                        @else
                            <div class="flex items-center">
                                <span class="w-3 h-3 rounded-full bg-blue-500 mr-2"></span>
                                <span class="text-[10px] text-gray-600 leading-tight">Online</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-3 h-3 rounded-full bg-amber-500 mr-2"></span>
                                <span class="text-[10px] text-gray-600 leading-tight">Semi Online</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Recap Tables --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Status Table --}}
        <div>
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-800">Rekap Status Pelaksanaan ANBK</h3>
            </div>

            {{-- Header Summary Cards for Table 1 --}}
            <div class="grid grid-cols-3 gap-2 mb-4">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded bg-green-100 text-green-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold">
                            {{ number_format($this->stats['totals']['status']['mandiri'], 0, ',', '.') }}</div>
                        <div class="text-[10px] text-gray-500">Mandiri</div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded bg-blue-100 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold">
                            {{ number_format($this->stats['totals']['status']['menumpang'], 0, ',', '.') }}</div>
                        <div class="text-[10px] text-gray-500">Menumpang</div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded bg-yellow-100 text-yellow-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold">
                            {{ number_format($this->stats['totals']['status']['belum'], 0, ',', '.') }}</div>
                        <div class="text-[10px] text-gray-500">Belum Ditetapkan</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-900 text-white">
                        <tr>
                            <th class="p-3">No</th>
                            <th class="p-3">Jenjang</th>
                            <th class="p-3 text-right">Mandiri</th>
                            <th class="p-3 text-right">Menumpang</th>
                            <th class="p-3 text-right">Belum Menentukan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($this->stats['tableStatus'] as $index => $row)
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 text-center border-r border-gray-200">{{ $index + 1 }}</td>
                                <td class="p-3 font-medium border-r border-gray-200">{{ $row['jenjang'] }}</td>
                                <td class="p-3 text-right border-r border-gray-200">
                                    {{ number_format($row['mandiri'], 0, ',', '.') }}</td>
                                <td class="p-3 text-right border-r border-gray-200">
                                    {{ number_format($row['menumpang'], 0, ',', '.') }}</td>
                                <td class="p-3 text-right">{{ number_format($row['belum'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Moda Table --}}
        <div>
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-800">Rekap Moda Pelaksanaan ANBK</h3>
            </div>

            {{-- Header Summary Cards for Table 2 --}}
            <div class="grid grid-cols-3 gap-2 mb-4">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded bg-blue-100 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold">
                            {{ number_format($this->stats['totals']['moda']['online'], 0, ',', '.') }}</div>
                        <div class="text-[10px] text-gray-500">Online</div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold">
                            {{ number_format($this->stats['totals']['moda']['semi'], 0, ',', '.') }}</div>
                        <div class="text-[10px] text-gray-500">Semi Online</div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded bg-yellow-100 text-yellow-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold">
                            {{ number_format($this->stats['totals']['moda']['belum'], 0, ',', '.') }}</div>
                        <div class="text-[10px] text-gray-500">Belum Menentukan</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-900 text-white">
                        <tr>
                            <th class="p-3">No</th>
                            <th class="p-3">Jenjang</th>
                            <th class="p-3 text-right">Online</th>
                            <th class="p-3 text-right">Semi Online</th>
                            <th class="p-3 text-right">Belum Menentukan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($this->stats['tableModa'] as $index => $row)
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 text-center border-r border-gray-200">{{ $index + 1 }}</td>
                                <td class="p-3 font-medium border-r border-gray-200">{{ $row['jenjang'] }}</td>
                                <td class="p-3 text-right border-r border-gray-200">
                                    {{ number_format($row['online'], 0, ',', '.') }}</td>
                                <td class="p-3 text-right border-r border-gray-200">
                                    {{ number_format($row['semi'], 0, ',', '.') }}</td>
                                <td class="p-3 text-right">{{ number_format($row['belum'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Regional Recap Table --}}
    <div class="mt-12">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-gray-800 text-lg border-b-2 border-blue-500 pb-1 inline-block">Rekap Sulawesi
                Tengah</h3>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-900 text-white">
                    <tr>
                        <th rowspan="2" class="p-3 border-r border-slate-700 w-12 text-center align-middle">No</th>
                        <th rowspan="2" class="p-3 border-r border-slate-700 align-middle">Kota / Kabupaten</th>
                        <th colspan="3"
                            class="p-3 border-r border-slate-700 text-center border-b border-slate-700">
                            Status Pelaksanaan</th>
                        <th colspan="3" class="p-3 text-center border-b border-slate-700">Moda Pelaksanaan</th>
                    </tr>
                    <tr>
                        <th class="p-2 text-center border-r border-slate-700 bg-slate-800">Mandiri</th>
                        <th class="p-2 text-center border-r border-slate-700 bg-slate-800">Menumpang</th>
                        <th class="p-2 text-center border-r border-slate-700 bg-slate-800">Belum Ditetapkan</th>
                        <th class="p-2 text-center border-r border-slate-700 bg-slate-800">Online</th>
                        <th class="p-2 text-center border-r border-slate-700 bg-slate-800">Semi Online</th>
                        <th class="p-2 text-center bg-slate-800">Belum Menentukan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($this->stats['tableWilayah'] as $index => $row)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 text-center border-r border-gray-200">{{ $index + 1 }}</td>
                            <td class="p-3 font-medium text-blue-600 border-r border-gray-200">{{ $row['wilayah'] }}
                            </td>

                            {{-- Status --}}
                            <td class="p-3 text-right border-r border-gray-200">
                                {{ number_format($row['status']['mandiri'], 0, ',', '.') }}</td>
                            <td class="p-3 text-right border-r border-gray-200">
                                {{ number_format($row['status']['menumpang'], 0, ',', '.') }}</td>
                            <td class="p-3 text-right border-r border-gray-200">
                                {{ number_format($row['status']['belum'], 0, ',', '.') }}</td>

                            {{-- Moda --}}
                            <td class="p-3 text-right border-r border-gray-200">
                                {{ number_format($row['moda']['online'], 0, ',', '.') }}</td>
                            <td class="p-3 text-right border-r border-gray-200">
                                {{ number_format($row['moda']['semi'], 0, ',', '.') }}</td>
                            <td class="p-3 text-right">{{ number_format($row['moda']['belum'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('donutChart', (series, labels, colors) => ({
                initChart() {
                    let options = {
                        series: series,
                        labels: labels,
                        colors: colors,
                        chart: {
                            type: 'donut',
                            height: 140,
                            fontFamily: 'inherit',
                            sparkline: {
                                enabled: true
                            }
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '75%',
                                    labels: {
                                        show: false
                                    }
                                }
                            }
                        },
                        legend: {
                            show: false
                        },
                        dataLabels: {
                            enabled: false
                        },
                        tooltip: {
                            y: {
                                formatter: function(val) {
                                    return val
                                }
                            }
                        },
                        stroke: {
                            show: false
                        }
                    };

                    let chart = new ApexCharts(this.$refs.chart, options);
                    chart.render();
                }
            }));
        });
    </script>
</div>
