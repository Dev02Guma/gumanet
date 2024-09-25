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
            text: `<p class="font-weight-bolder">PRESUPESTO UNIDADES VS EJECUTADO </p>`
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
            enabled:true,
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

function isValue(value, def, is_return) {
    if ( $.type(value) == 'null'
        || $.type(value) == 'undefined'
        || $.trim(value) == '(en blanco)'
        || $.trim(value) == ''
        || ($.type(value) == 'number' && !$.isNumeric(value))
        || ($.type(value) == 'array' && value.length == 0)
        || ($.type(value) == 'object' && $.isEmptyObject(value)) ) {
        return ($.type(def) != 'undefined') ? def : false;
    } else {
        return ($.type(is_return) == 'boolean' && is_return === true ? value : true);
    }
}

function bluid_char(ARTICULO,Pro, tipo) {
    
    var temporal = "";

    $("#grafSkuAnual").empty().append(`<div style="height:400px; background:#ffff; padding:20px">
                <div class="d-flex align-items-center">
                    <strong class="text-info">Cargando...</strong>
                    <div class="spinner-border ml-auto text-primary" role="status" aria-hidden="true"></div>
                </div>
            </div>`);
    if (Pro === 1) {
        f1 = $("#f1").val();
        f2 = $("#f2").val();
    } else {
        f1 = $("#f1_p71").val();
        f2 = $("#f2_p71").val();
    }

    SkusAnual.series = [];
    SkusAnual.xAxis.categories = [];
    
    $.getJSON("dtArticulo?f1="+f1+"&f2="+f2+"&ARTICULO=" + ARTICULO+"&Pro=" + Pro +"&tipo="+tipo, function(json) {
        var SeriesVenta;
        var SeriesMetas;
        var sumTotales = [];
        var temp = 0;
        var anio = 0;
        var date  = new Date();
        var anio_ = parseInt(date.getFullYear());
        var mes_ = parseInt(date.getMonth()+1);

        units = "";
        monto = "";
        if(tipo == 1){
            units = 'Units';
        }
        if(tipo == 2){
            monto = 'C$';
        }

        var VENTAS  = []
        var METAS   = []
        var LEGNS   = []

        
        $.each(json[0]['FECHA'], function (i, item) {
            VENTAS.push(parseFloat( isValue(json[0][item.mes],0,true) ));
            METAS.push(parseFloat( isValue(json[0].UND_MES,0,true) ));
            LEGNS.push(item.mes)
            SkusAnual.xAxis.categories.push(item.mes);
        })    

       

        SeriesVenta = {};
        SeriesVenta.data = VENTAS;
        SeriesVenta.name = 'EJECUTADO';
        SeriesVenta.color = colors_[1];
        SkusAnual.series.push(SeriesVenta);

        if (Pro != 2) {
            SeriesVenta = {};
            SeriesVenta.data = METAS;
            SeriesVenta.name = 'PRESUPUESTO';
            SeriesVenta.color = colors_[2];
            SkusAnual.series.push(SeriesVenta);
        }
        
        

        SkusAnual.tooltip = {
            pointFormat : '<b>'+monto+' <span style="color:black"><b>{point.y:,.0f} '+units+' </b></span>'
        };

        var chart = new Highcharts.Chart(SkusAnual);
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