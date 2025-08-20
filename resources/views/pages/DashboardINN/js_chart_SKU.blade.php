<script>
function renderSKUPieChart(data) {
    const totalPeso = data.reduce((sum, item) => sum + parseFloat(item.PESO), 0);

    const colores = [
        '#4E79A7', '#F28E2B', '#E15759', '#76B7B2',
        '#59A14F', '#EDC949', '#AF7AA1', '#FF9DA7',
        '#9C755F', '#BAB0AC'
    ];

    const chartData = data.map((item, index) => ({
        name: item.DESCRIPCION,
        y: parseFloat(((item.PESO / totalPeso) * 100).toFixed(2)),
        color: colores[index % colores.length]
    }));

    Highcharts.chart('container', {
        chart: {
            type: 'pie'
        },
        title: {
            text: 'PARTICIPACION POR SKU (%)'
        },
        tooltip: {
            pointFormat: '<b>{point.y:.2f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        exporting: {
            enabled: false
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '{point.y:.2f}%',
                    distance: -30,
                    color: 'white',
                    style: {
                        fontWeight: 'bold',
                        textOutline: '1px contrast'
                    }
                },
                showInLegend: false
            }
        },
        legend: {
            layout: 'horizontal',
            align: 'center',
            verticalAlign: 'bottom',
            itemMarginTop: 5,
            itemMarginBottom: 5
        },
        series: [{
            name: 'Participación',
            colorByPoint: false,
            data: chartData
        }]
    });
}
</script>
