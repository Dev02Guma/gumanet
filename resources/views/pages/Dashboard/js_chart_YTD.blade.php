<script>
    
function renderComparativaYTD(data, tipo = 'valor') {
  const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

  let dta1 = [], dta2 = [], categories_ = [];
  const anio1 = new Date().getFullYear() - 1;
  const anio2 = new Date().getFullYear();

  data.forEach((item) => {
  const ventaAnterior = tipo === 'valor' ? item.Venta_Anterior || 0 : item.Cantidad_Anterior || 0;
    const ventaActual = tipo === 'valor' ? item.Venta_Actual || 0 : item.Cantidad_Actual || 0;

    dta1.push(ventaAnterior);
    dta2.push(ventaActual);

    const crecimiento = ventaAnterior !== 0 ? ((ventaActual / ventaAnterior) - 1) * 100 : 0;

    categories_.push({
      name: numeral(crecimiento).format('0,0.00') + '%',
      categories: [{
        name: numeral(ventaActual).format('0,0.00'),
        categories: [{
          name: numeral(ventaAnterior).format('0,0.00'),
          categories: [meses[item.Mes - 1]]
        }]
      }]
    });
  });

  Highcharts.chart('chart_ytd', {
    chart: {
      type: 'column'
    },
    title: {
      text: `COMPARATIVA DE ${tipo === 'valor' ? 'VENTAS' : 'UNIDADES'} YTD`
    },
    xAxis: {
      categories: categories_,
      labels: {
        rotation: 0,
        style: {
          fontSize: '11px'
        }
      }
    },
    exporting: {
        enabled: false  
    },
    tooltip: {
      headerFormat: '<span style="font-size:10px"></span><table>',
      pointFormat: `<tr><td style="color:{series.color};padding:0">{series.name}: </td>` +
                   `<td style="padding:0"><b>${tipo === 'valor' ? 'C$' : 'UND'} {point.y:,.2f}</b></td></tr>`,
      footerFormat: '</table>',
      shared: true,
      useHTML: true
    },
    legend: {
      layout: 'vertical',
      align: 'left',
      verticalAlign: 'bottom',
      floating: true,
      backgroundColor: '#FFFFFF',
      itemMarginTop: 2,
      itemMarginBottom: 2,
      x: -10,
      y: -25
    },
    plotOptions: {
      column: {
        pointPadding: 0.1,
        borderWidth: 0
      }
    },
   series: [{
      name: tipo === 'valor' ? anio1+' C$' : anio1,
      data: dta1,
      color: tipo === 'valor' ? '#8e44ad' : '#8e44ad'
    }, {
      name: tipo === 'valor' ? anio2+' C$' : anio2,
      data: dta2,
      color: tipo === 'valor' ? '#fc5404' : '#fc5404'
    }]
  });
}

</script>

