<div>
    <div id="jenjangChart"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const data = @json($data);

        const options = {
            series: data.map(d => d.count),
            chart: {
                type: 'donut',
                height: 350
            },
            labels: data.map(d => d.nama),
            colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899'],
            legend: {
                position: 'bottom'
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%'
                    }
                }
            }
        };

        const chart = new ApexCharts(document.querySelector("#jenjangChart"), options);
        chart.render();
    });
</script>
