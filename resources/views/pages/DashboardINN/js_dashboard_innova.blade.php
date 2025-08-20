  <script>
  $(document).ready(function() {
      //inicializaControlFecha();
      fullScreen();

      $('input[name="dt_range"]').daterangepicker({
          "autoApply": true,
          ranges: {
              'Hoy': [moment(), moment()],
              'Últimos 7 Días': [moment().subtract(6, 'days'), moment()],
              'Últimos 30 Días': [moment().subtract(29, 'days'), moment()],
              'Semana Anterior': [moment().subtract(1, 'week').startOf('week'), moment().subtract(1, 'week').endOf('week')],
              'Esta Semana': [moment().startOf('week'), moment().endOf('week')],
              'Mes Anterior' : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
              'Este mes a la Fecha': [moment().startOf('month'), moment()],
          },
          "showCustomRangeLabel": false,
          "alwaysShowCalendars": true,
          "startDate": moment().format('D MMM. YYYY'),
          "endDate": moment().format('D MMM. YYYY'),
          opens: 'left',
          locale: {
              //format: "DD/MM/YYYY",
              format: "D MMM. YYYY",   // Ejemplo: 1 ago. 2025
              separator: " - ",
              applyLabel: "Aplicar",
              cancelLabel: "Cancelar",
              fromLabel: "Desde",
              toLabel: "Hasta",
              customRangeLabel: "Personalizado",
              weekLabel: "S",
              daysOfWeek: ["Dom.", "Lun.", "Mar.", "Mie.", "Jue.", "Vie", "Sab."],
              monthNames: [
                  "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                  "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
              ],
              firstDay: 1
          }
      }, function(start, end, label) {
          //console.log('Nuevo rango seleccionado: ' + start.format('YYYY-MM-DD') + ' a ' + end.format('YYYY-MM-DD') + ' (rango: ' + label + ')');
          CallFilter(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
      });



      $('#filtrarFechas').on('click', function() {

          var desde = $('input[name="dt_range"]').data('daterangepicker').startDate.format('YYYY-MM-DD');
          var hasta = $('input[name="dt_range"]').data('daterangepicker').endDate.format('YYYY-MM-DD');

          CallFilter( desde, hasta );        
      });

      


      $("#id_search_importaciones").on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase();
        $('#tbl_topsku_clientes').DataTable().search(searchTerm).draw();
      });


  });


  function CallFilter( desde = null, hasta = null ) {

      $("#tl_periodo").html(`<b>${desde}</b> a <b>${hasta}</b>`);
      
      cargarGetDataInnova(desde, hasta);
    
  }
  function OnWay() {
    swal.fire({
      title: 'En Construcción',
      text: 'Esta sección está en desarrollo y estará disponible pronto.',
      icon: 'info',
      confirmButtonText: 'Aceptar'
    });
  }
  function eneableButton(EnableButton, textButton = '<i class="fas fa-filter"></i> Filtrar') {
    $('#filtrarFechas').prop('disabled', EnableButton);
    $('#filtrarFechas').html('<i class="fas fa-spinner fa-spin" style="display:' + (EnableButton ? 'inline-block' : 'none') + '"></i> ' + textButton);
  }

    function loadAndBuildTable(selector, data) {
      $(selector).DataTable({
        data: data,
        destroy: true,
        paging: true,
        pageLength: 7,
        info: false,
        searching: false,
        ordering: false,
        columns: [
          { data: 'NOMBRE', render: function(data, type, row) {
            return `<div class="item-left">${data}<br><span class="item-sub">${row.CODIGO}</span></div>`;
          }},
          { data: 'BULTOS_TOTAL_NIO', render: function(data, type, row) {
            return `<div class="item-right">C$ ${data}<br><span class="item-sub">${row.BULTOS_TOTAL_UND}</span></div>`;
          }},
        ],
      });
      $(selector + '_length').hide();
    }
    function TBL_TOP_SKU(selector, data) {
      var table = $(selector).DataTable({
        data: data,
        destroy: true,
        paging: true,
        pageLength: 7,
        info: false,
        searching: false,
        ordering: false,
        columns: [
          { data: 'DESCRIPCION', render: function(data, type, row) { return `<div class="item-left">${data}<br><span class="item-sub">${row.SKU}</span></div>`;}},
          { data: 'BULTOS_TOTAL_NIO', render: function(data, type, row) {
            return `<div class="item-right">
                  C$ ${numeral(data).format('0,0.00')}<br>
                  <span class="item-sub">${numeral(row.BULTOS_TOTAL_UND).format('0,0')} Bls.</span>
                </div>`;}          
          },
          { data: 'PESO', render: function(data, type, row) {
              return `<div class="item-right">${numeral(data).format('0,0.00')} %</div>`;
            }          
          }
        ],
        createdRow: function (row, rowData) {
          $(row).on('click', function() {
            var data = table.row(this).data();
            $('#mdl-topsku').modal('show');
            $('#id-name-articulo').text(data.DESCRIPCION );
            getDetallesSKUCliente(data.SKU);
            excelSku(data.SKU);
            
          });
        },
      });

    
      $(selector + '_length').hide();
    }

    function TBL_TOP_SKU_CLIENTES(Dt) {        
        // Populate the table with data
        $("#tbl_topsku_clientes").DataTable({
            data: Dt,
            destroy: true,
            order: [],
            columns: [
                { data: "CLIENTE", title: "CLIENTE" },
                { data: "NOMBRE", title: "NOMBRE" },
                { data: "CANTIDAD", title: "CANTIDAD" ,  class: "text-right", render: $.fn.dataTable.render.number(',', '.', 0, '') },
                { data: "VENTA_SIN_IVA", title: "VENTA SIN IVA",   class: "text-right", render: $.fn.dataTable.render.number(',', '.', 0, '') },
                { data: "VENTA_CON_IVA", title: "VENTA CON IVA",  class: "text-right", render: $.fn.dataTable.render.number(',', '.', 0, '') }
            ],
            pageLength: 7,
            bLengthChange: false,
            searching: true,
        });
        
        $("#tbl_topsku_clientes_filter").hide();
    }
    async function getDetallesSKUCliente(articulo) {
      try {
        const desde = $('#desdeInnova').val();
        const hasta = $('#hastaInnova').val();

        const response = await fetch('getDetallesSKUCliente', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ 
            desde     : desde, 
            hasta     : hasta,
            articulo  : articulo
          })
        });
        const result = await response.json();

        TBL_TOP_SKU_CLIENTES(result);
      } catch (error) {
        console.error(error);
      }
    }
    async function cargarGetDataInnova(desde, hasta){
      
        try {
            eneableButton(true,'Calc...')   
            
            const response = await fetch('getDataInnova', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ 
                  desde: desde, 
                  hasta: hasta 
                })
            });

            const result = await response.json();

            loadAndBuildTable('#clientesTable', result.ACTUAL.Clientes);            
            loadAndBuildTable('#vendedoresTable', result.ACTUAL.Vendedores);
            TBL_TOP_SKU('#tbl_top_sku', result.ACTUAL.SKU_CHART.data);
            loadAndBuildTable('#tbl_top_clientes', result.ACTUAL.CLS_CHART);
            renderSKUPieChart(result.ACTUAL.SKU_CHART.data);
            renderClienteBolsonChart(result.ACTUAL.CLS_CHART);

            let datos = [];
            let totales = [];
            
            datos = result.COMPARATIVAYTD.COMPARATIVA_YTD;
            totales = result.COMPARATIVAYTD;

            renderComparativaYTD(datos, 'valor');

            $('#bultos_facturacion').text("C$ " + result.ACTUAL.Metricas.BULTOS_TOTAL_NIO);
            $('#bultos_valor').text(result.ACTUAL.Metricas.BULTOS_TOTAL_UND);
            $('#bultos_actual').text(result.COMPARATIVA.UND_YTD.BULTOS_UND_ANIO_ACTUAL);
            $('#bultos_anterior').text(result.COMPARATIVA.UND_YTD.BULTOS_UND_ANIO_ANTERIOR);
            $('#fechaClienteFact').text(result.ACTUAL.HASTA);
            $('#fechaVentaVendedor').text(result.ACTUAL.HASTA);
            $('#fechaSKU').text(result.ACTUAL.DESDE + ' al ' + result.ACTUAL.HASTA);
            $('#fechaVentaNeta').text(result.ACTUAL.DESDE + ' al ' + result.ACTUAL.HASTA);

            $('#ytd_anterior').text('C$ '+ numeral(result.COMPARATIVAYTD.YTD_VALOR_ANTERIOR).format('0,0.00'));
            $('#ytd_actual').text('C$ '+ numeral(result.COMPARATIVAYTD.YTD_VALOR_ACTUAL).format('0,0.00'));
            $('#ytd_crecimiento').text(numeral(result.COMPARATIVAYTD.YTD_VALOR_CRECIMIENTO).format('0,0.00'));

            $("#anioAnterior").text(new Date().getFullYear() - 1);
            $("#anioActual").text(new Date().getFullYear());
            $("#total_sku_bultos").text(result.ACTUAL.SKU_CHART.Totals.Bultos + " Bls.");
            $("#total_sku_valor").text("C$ " + result.ACTUAL.SKU_CHART.Totals.Valor);
            $("#total_Cliente_bultos").text(result.ACTUAL.SKU_CHART.Totals.Bultos + " Bls.");
            $("#total_Cliente_valor").text("C$ " + result.ACTUAL.SKU_CHART.Totals.Valor);


            //Declarar la variable como global
            window.datos = datos;
            window.totales = totales;

            eneableButton(false,'<i class="fas fa-filter"></i> Filtrar')

        } catch (error) {
            console.error('Error al obtener los datos:', error);
            eneableButton(false,null)
        }
    }

    //Funcion para actualizar el grafico ytd segun el tipo que se escoja 
    function actualizarGraficoYTD() {
      const tipo = document.getElementById("tipoDato").value;
      
      //Las variable se actualizaran en funcion del tipo de dato que se necesite
      $('#ytd_anterior').text(tipo === 'valor' ? 'C$ '+ numeral(window.totales.YTD_VALOR_ANTERIOR).format('0,0.00') : numeral(window.totales.YTD_UND_ANTERIOR).format('0,0'));
      $('#ytd_actual').text(tipo === 'valor' ? 'C$ '+ numeral(window.totales.YTD_VALOR_ACTUAL).format('0,0.00') : numeral(window.totales.YTD_UND_ACTUAL).format('0,0'));
      $('#ytd_crecimiento').text(tipo === 'valor' ? numeral(window.totales.YTD_VALOR_CRECIMIENTO).format('0,0.00') : numeral(window.totales.YTD_UND_CRECIMIENTO).format('0,0.00'));
      
      //Llamada a la funcion que se encuentra en js_chart_YTD.blade
      renderComparativaYTD(window.datos, tipo);
    }

    function excelSku(articulo){

      $("#exp-to-excel").click(function() {    
        const desde = $('#desdeInnova').val();
        const hasta = $('#hastaInnova').val();
        location.href = "getExcelSku?desde=" + encodeURIComponent(desde) + "&hasta=" + encodeURIComponent(hasta) + "&articulo=" + encodeURIComponent(articulo);
      })
    }

    document.addEventListener('DOMContentLoaded', async () => {
      const hoyDesde = new Date().toISOString().split('T')[0]; 
      $('#desdeInnova').val(hoyDesde);
      const hoyHasta = new Date().toISOString().split('T')[0]; 
      $('#hastaInnova').val(hoyHasta);

      cargarGetDataInnova(hoyDesde, hoyHasta);
        
    });


</script>