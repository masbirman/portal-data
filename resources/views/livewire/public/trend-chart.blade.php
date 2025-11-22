<div class="mt-8 bg-white rounded-lg shadow-lg p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Tren Capaian Asesmen</h3>
            <p class="text-gray-600 text-sm">Perkembangan jumlah peserta dan tingkat keikutsertaan.</p>
        </div>
        <!-- Optional: Add a year filter or legend here if needed -->
    </div>

    <div x-data="trendChart(@js($chartData))" x-init="initChart" class="w-full h-[350px]">
        <div x-ref="chart"></div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('trendChart', (data) => ({
                initChart() {
                    // Transform series to mixed type
                    const series = [
                        {
                            name: 'Peserta Asesmen',
                            type: 'area',
                            data: data.series[0].data
                        },
                        {
                            name: 'Keikutsertaan (%)',
                            type: 'line',
                            data: data.series[1].data
                        }
                    ];

                    let options = {
                        series: series,
                        chart: {
                            height: 350,
                            type: 'line', // Base type
                            toolbar: { show: false },
                            zoom: { enabled: false },
                            fontFamily: 'Inter, sans-serif'
                        },
                        colors: ['#3B82F6', '#F97316'], // Blue-500, Orange-500
                        fill: {
                            type: ['gradient', 'solid'],
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.7,
                                opacityTo: 0.2,
                                stops: [0, 90, 100]
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            enabledOnSeries: [1],
                            style: {
                                colors: ['#F97316']
                            },
                            background: {
                                enabled: true,
                                foreColor: '#fff',
                                borderRadius: 4,
                                padding: 4,
                                opacity: 0.9,
                                borderWidth: 1,
                                borderColor: '#F97316'
                            }
                        },
                        stroke: {
                            curve: 'smooth',
                            width: [0, 4] // 0 width for area stroke (optional, or 2), 4 for line
                        },
                        markers: {
                            size: [4, 6],
                            colors: ['#fff', '#F97316'],
                            strokeColors: ['#3B82F6', '#fff'],
                            strokeWidth: 2,
                            hover: {
                                size: 8
                            }
                        },
                        xaxis: {
                            categories: data.categories,
                            title: { text: 'Tahun Periode' },
                            axisBorder: { show: false },
                            axisTicks: { show: false }
                        },
                        yaxis: [
                            {
                                title: { 
                                    text: 'Jumlah Peserta',
                                    style: { color: '#3B82F6' }
                                },
                                labels: {
                                    style: { colors: '#3B82F6' },
                                    formatter: (val) => val.toLocaleString('id-ID')
                                }
                            },
                            {
                                opposite: true,
                                title: { 
                                    text: 'Keikutsertaan (%)',
                                    style: { color: '#F97316' }
                                },
                                max: 100,
                                labels: {
                                    style: { colors: '#F97316' },
                                    formatter: (val) => val + "%"
                                }
                            }
                        ],
                        grid: {
                            borderColor: '#f3f4f6',
                            strokeDashArray: 4,
                            xaxis: { lines: { show: true } }   
                        },
                        tooltip: {
                            shared: true,
                            intersect: false,
                            y: {
                                formatter: function (y, { seriesIndex }) {
                                    if(seriesIndex === 0) return y.toLocaleString('id-ID') + " Siswa";
                                    return y + "%";
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'right',
                            markers: { radius: 12 }
                        }
                    };

                    let chart = new ApexCharts(this.$refs.chart, options);
                    chart.render();
                }
            }));
        });
    </script>
</div>
