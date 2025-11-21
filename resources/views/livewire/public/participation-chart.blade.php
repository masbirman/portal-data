<div>
    <div id="participationChart"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const data = @json($data);

        const options = {
            series: [{
                name: 'Literasi',
                data: data.map(d => d.literasi)
            }, {
                name: 'Numerasi',
                data: data.map(d => d.numerasi)
            }],
            chart: {
                type: 'line',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            colors: ['#3B82F6', '#10B981'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: data.map(d => d.tahun),
                title: {
                    text: 'Tahun'
                }
            },
            yaxis: {
                title: {
                    text: 'Partisipasi (%)'
                },
                min: 0,
                max: 100
            },
            legend: {
                position: 'top'
            }
        };

        const chart = new ApexCharts(document.querySelector("#participationChart"), options);
        chart.render();
    });
</script>
