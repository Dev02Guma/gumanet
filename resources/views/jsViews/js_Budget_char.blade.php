<script type="text/javascript">
var SkusAnual           = {};
var colors_ = ['#407EC9', '#D19000', '#00A376', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'];

SkusAnual = {
        chart: {
            type: 'spline',
            renderTo: 'grafSkuAnual'
        },
        exporting: {enabled: false},
        title: {
            text: `<p class="font-weight-bolder">VENTAS EN PROMEDIO </p>`
        },
        xAxis: {
            categories: []
        },
        yAxis: {
            title: {
                text: ''
            }                
        },
        plotOptions: {
            series: {
                allowPointSelect: false,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        return FormatPretty(this.y);
                    }
                },
                events: {
                    legendItemClick: function() {
                        return false;
                    }
                },
                cursor: 'pointer',
                point: {
                    events: {
                        click: function() {
                            //promedio_comportamiento("SKUs","")
                        }
                    }
                }
            },
        },
        tooltip: {},
        legend: {
            enabled:false,
            align: 'center',
            verticalAlign: 'top',
            borderWidth: 0
        },
        series: [],
        responsive: {
            rules: [{
                condition: {
                maxWidth: 500
                },
                chartOptions: {
                    legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                    }
                }
            }]
        }
    };

function bluid_char(ARTICULO) {
    var temporal = "";
    $("#grafSkuAnual").empty().append(`<div style="height:400px; background:#ffff; padding:20px">
                <div class="d-flex align-items-center">
                    <strong class="text-info">Cargando...</strong>
                    <div class="spinner-border ml-auto text-primary" role="status" aria-hidden="true"></div>
                </div>
            </div>`);


    $("#anioAcumulado").empty();
    $("#porcentaje").empty();
    
    f1 = $("#f1").val();
    f2 = $("#f2").val();

    SkusAnual.series = [];
    SkusAnual.xAxis.categories = [];
    $.getJSON("dtArticulo?f1="+f1+"&f2="+f2+"&ARTICULO=" + ARTICULO, function(json) {
        var SeriesVenta;
        var SeriesMetas;
        var sumTotales = [];
        var temp = 0;
        var anio = 0;
        var date  = new Date();
        var anio_ = parseInt(date.getFullYear());
        var mes_ = parseInt(date.getMonth()+1);

        var VENTAS  = []
        var METAS   = []

        
        $.each(json[0]['FECHA'], function (i, item) {
         
            temporal = '<span style="color:black"><b>{point.y:,.0f} Items </b></span>';

            VENTAS.push(parseFloat(json[0][item.mes]));
            METAS.push(parseFloat(json[0].UND_MES));

            SeriesVenta = {};
            SeriesVenta.data = VENTAS;
            SeriesVenta.name = 'VENTAS';
            SeriesVenta.color = colors_[1];
            SkusAnual.series.push(SeriesVenta);

            SeriesVenta = {};
            SeriesVenta.data = METAS;
            SeriesVenta.name = 'METAS';
            SeriesVenta.color = colors_[2];
            SkusAnual.series.push(SeriesVenta);
            
            SkusAnual.xAxis.categories.push(item.mes);

            SkusAnual.tooltip = {
                pointFormat : temporal
            };

            var chart = new Highcharts.Chart(SkusAnual);
            
        })    
    })    
        
    
}

function FormatPretty(number) {
    var numberString;
    var scale = '';
    if( isNaN( number ) || !isFinite( number ) ) {
        numberString = 'N/A';
    } else {
        var negative = number < 0;
        number = negative? -number : number;

        if( number < 1000 ) {
            scale = '';
        } else if( number < 1000000 ) {
            scale = 'K';
            number = number/1000;
        } else if( number < 1000000000 ) {
            scale = 'M';
            number = number/1000000;
        } else if( number < 1000000000000 ) {
            scale = 'B';
            number = number/1000000000;
        } else if( number < 1000000000000000 ) {
            scale = 'T';
            number = number/1000000000000;
        }
        var maxDecimals = 0;
        if( number < 10 && scale != '' ) {
            maxDecimals = 1;
        }
        number = negative ? -number : number;
        numberString = number.toFixed( maxDecimals );
        numberString += scale
    }
    return numberString;
}
    
</script>