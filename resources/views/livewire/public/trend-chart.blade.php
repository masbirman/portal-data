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
                    const series = [{
                        name: 'Peserta Asesmen',
                        type: 'area',
                        data: data.series[0].data
                    }, {
                        name: 'Keikutsertaan (%)',
                        type: 'line',
                        data: data.series[1].data
                    }];

                    let options = {
                        series: series,
                        chart: {
                            height: 350,
                            type: 'line', // Base type
                            toolbar: {
                                show: false
                            },
                            zoom: {
                                enabled: false
                            },
                            fontFamily: 'Instrument Sans, sans-serif',
                            background: 'transparent'
                        },
                        colors: ['#0f172a', '#f59e0b'], // Slate-900, Amber-500
                        fill: {
                            type: ['gradient', 'solid'],
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.4, // More subtle opacity
                                opacityTo: 0.05,
                                stops: [0, 100]
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            enabledOnSeries: [1],
                            style: {
                                colors: ['#f59e0b'],
                                fontSize: '12px',
                                fontWeight: 600,
                            },
                            background: {
                                enabled: true,
                                foreColor: '#fff',
                                borderRadius: 6,
                                padding: 6,
                                opacity: 1,
                                borderWidth: 1,
                                borderColor: '#f59e0b'
                            }
                        },
                        stroke: {
                            curve: 'smooth',
                            width: [2, 4], // Thinner area stroke
                            dashArray: [0, 0]
                        },
                        markers: {
                            size: [4, 6],
                            colors: ['#fff', '#f59e0b'],
                            strokeColors: ['#0f172a', '#fff'],
                            strokeWidth: 2,
                            hover: {
                                size: 8
                            }
                        },
                        xaxis: {
                            categories: data.categories,
                            title: {
                                text: 'Tahun Periode',
                                style: {
                                    color: '#64748b',
                                    fontSize: '12px'
                                }
                            },
                            axisBorder: {
                                show: false
                            },
                            axisTicks: {
                                show: false
                            },
                            labels: {
                                style: {
                                    colors: '#64748b'
                                }
                            }
                        },
                        yaxis: [{
                            title: {
                                text: 'Jumlah Peserta',
                                style: {
                                    color: '#0f172a',
                                    fontWeight: 600
                                }
                            },
                            labels: {
                                style: {
                                    colors: '#64748b'
                                },
                                formatter: (val) => val.toLocaleString('id-ID')
                            }
                        }, {
                            opposite: true,
                            title: {
                                text: 'Keikutsertaan (%)',
                                style: {
                                    color: '#f59e0b',
                                    fontWeight: 600
                                }
                            },
                            max: 100,
                            labels: {
                                style: {
                                    colors: '#64748b'
                                },
                                formatter: (val) => val + "%"
                            }
                        }],
                        grid: {
                            borderColor: '#f1f5f9',
                            strokeDashArray: 4,
                            xaxis: {
                                lines: {
                                    show: true
                                }
                            },
                            yaxis: {
                                lines: {
                                    show: true
                                }
                            },
                            padding: {
                                top: 0,
                                right: 0,
                                bottom: 0,
                                left: 10
                            }
                        },
                        tooltip: {
                            theme: 'light',
                            shared: true,
                            intersect: false,
                            y: {
                                formatter: function(y, {
                                    seriesIndex
                                }) {
                                    if (seriesIndex === 0) return y.toLocaleString('id-ID') +
                                        " Siswa";
                                    return y + "%";
                                }
                            },
                            style: {
                                fontSize: '12px'
                            },
                            marker: {
                                show: true,
                            },
                            x: {
                                show: true,
                            }
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'right',
                            fontFamily: 'Instrument Sans, sans-serif',
                            fontWeight: 500,
                            labels: {
                                colors: '#475569'
                            },
                            markers: {
                                radius: 12
                            }
                        }
                    };

                    let chart = new ApexCharts(this.$refs.chart, options);
                    chart.render();
                }
            }));
        });
    </script>
</div>
