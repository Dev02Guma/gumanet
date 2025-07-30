<script>
	$(document).ready(function() {
        

		 $("#item-nav-01").after(`<li class="breadcrumb-item active">Recuperacion</li>`);// mostrar mapa de ubicación actual
        var mes = $('#selectMesIntroRecup option:selected').val();
        var anio = $('#selectAnnoIntroRecup option:selected').val();
        pageName    = 'Recuperacion';
        var route="getMoneyRecuRowsByRoutes/"+mes+"/"+anio+"/"+pageName;
        var metodo = 'GET';
        
        //fillDtIntroRecup(route, metodo);
       // validacion = validarSiDtMuestraRegistros();
        var ValidarTablaRecu = existenDatosEnTablaRecu(mes, anio);

        asignarRutaMascaras(mes, anio);

          if (ValidarTablaRecu.length > 0) {
            fillDtIntroRecup(route, metodo);
           
            $('#btnSaveIntroRecup').text('Actualizar');//si hay registro label del boton es igual a "Actualizar Registros"
            $('#btnSaveIntroRecup').val(1);

        }else{

            $('#btnSaveIntroRecup').text('Guardar');//si NO hay registro label del boton es igual a "Crear Registros"
            $('#btnSaveIntroRecup').val(0);
            obtenerRutasRecu();


        }
        

	});
    function asignarRutaMascaras(mes, anio){
       
         $.ajax({
           url : 'obtenerRutasRecu/'+mes+'/'+anio,
           method:'GET',
           async : true,
           success: function(res){
            rutas = Array();
                for (var i = 0; i < res.length; i++) {
                    rutas[i] = res[i].RECU_RUTA;
                    $("#recu_meta_"+rutas[i]).maskMoney({prefix:'C$',  thousands:',', decimal:'.', affixesStay: true});
                    $("#recu_credito_"+rutas[i]).maskMoney({prefix:'C$', thousands:',', decimal:'.', affixesStay: true});
                    $("#recu_contado_"+rutas[i]).maskMoney({prefix:'C$', thousands:',', decimal:'.', affixesStay: true});
                }

             
            }
            
           
        });
        
       
    }


    function fillDtIntroRecup(route, metodo){
        //Mostrar datos de recuperación en data table
        $('#dtIntroRecup').DataTable({
            'responsive': true,
            'autoWidth':false,
            'ajax':{
                'url':route,
                'method':metodo,
                'async' : false,
                'dataSrc': '',
            },        
                "destroy" : true,
                "info":    false,
                "lengthMenu": [[30], [30]],
                "language": {
                    "zeroRecords": "Cargando...",
                    "paginate": {
                        "first":      "Primera",
                        "last":       "Última ",
                        "next":       "Siguiente",
                        "previous":   "Anterior"
                    },
                    "lengthMenu": "MOSTRAR _MENU_",
                    "emptyTable": "NO HAY DATOS DISPONIBLES",
                    "search":     "BUSCAR"
                },
                'columns': [
                    { "title": "Ruta",      "data": "RECU_RUTA" },
                    { "title": "Vendedor", "data": "RECU_VENDE" },
                    { "title": "Meta",      "data": "RECU_META" },
                    { "title": "Recup. Crédito",      "data": "RECU_CREDITO" },
                    { "title": "Recup. Contado","data": "RECU_CONTADO" },
                    { "title": "Recup. Total",      "data": "RECU_TOTAL" },
                    { "title": "% Cump. Crédito",      "data": "RECU_CUMPLIMIENTO" },
                    //{ "title": 'Opciones',"data": "RECU_OPCIONES" },
                ],
                "columnDefs": [
                    {"width":"20%","targets":[1]},
                    {"width":"15%","targets":[2, 3, 4, 5, 6]},
                    {"className": "dt-center", "targets":[0, 1, 2, 3, 4, 5, 6]}
                ],
                "fnInitComplete": function () {
                    //validarSiDtMuestraRegistros();
                }
                
            });

        $('#dtIntroRecup_length').hide();//Ocultar select que muestra cantidad de registros por pagina
        $('#dtIntroRecup_filter').hide();//Esconde input de filtro de tabla por texto escrito
    }


	$('#InputDtSearchIntroRecup').on('keyup', function() {
        var table = $('#dtIntroRecup').DataTable();
        table.search(this.value).draw();
    });

    $( "#InputDtShowColumnsIntroRecupa").change(function() {
        var table = $('#dtIntroRecup').DataTable();
        table.page.len(this.value).draw();
    });

    function getAttr(inputAtt){
        var valMeta = 0.00;
        var valCredito = 0.00;
        var valContado = 0.00;

        var valRutaCredito = "";
        var valRutaContado = "";
        var id = "";
        var totalRecu = 0.00;
        var meta;



        var table = $('#dtIntroRecup').DataTable();
        id = "#"+inputAtt.id;
        ruta = id.substr(-3,3);

       


        /*$("#recu_meta_"+ruta).keydown(function (){
            $("#recu_meta_"+ruta).maskMoney({prefix:'C$',  thousands:',', decimal:'.', affixesStay: true});
           this.value = "C$"+this.value + ""+numeral((this.value).replace(/[C$]/g, '')).format('0,0.00');
        });

        $("#recu_credito_"+ruta).keydown(function (){
             $("#recu_credito_"+ruta).maskMoney({prefix:'C$', thousands:',', decimal:'.', affixesStay: true});
            this.value = 'C$'+ numeral((this.value).replace(/[^0-9.,C$]/g, '')).format('0,0.00');
        });

        $("#recu_contado_"+ruta).keydown(function (){
            $("#recu_contado_"+ruta).maskMoney({prefix:'C$', thousands:',', decimal:'.', affixesStay: true});
            this.value = 'C$'+numeral((this.value).replace(/[^0-9.,C$]/g, '')).format('0,0.00');
        });*/


        valMeta = $("#recu_meta_"+ruta).val();
        valCredito = $("#recu_credito_"+ruta).val();
        valContado = $("#recu_contado_"+ruta).val();

        valMeta =valMeta.replace(/[^0-9.]/g, '');
        valCredito = valCredito.replace(/[^0-9.]/g, '');
        valContado = valContado.replace(/[^0-9.]/g, '');
        
        meta = Number(valMeta);
        cumplimiento = Number($('#recu_cumplimiento_'+ruta).val().replace('%',""));
        
        totalRecu = Number(valCredito) + Number(valContado);
        $('#recu_total_'+ruta).text("C$" + numeral(totalRecu).format('0,0.00'));
        $('#recu_cumplimiento_'+ruta).text((meta <= 0)? '0.00%' : numeral((Number(valCredito)/meta)*100).format('0,0.00')+'%');
        
    }


     function obtenerRutasRecu(){
        var mes = $('#selectMesIntroRecup option:selected').val();
        var anio = $('#selectAnnoIntroRecup option:selected').val();
        var route = 'obtenerRutasRecu/'+mes+'/'+anio;
        var metodo = 'GET';
        fillDtIntroRecup(route, metodo);

    }


    function existenDatosEnTablaRecu(mes, anio, pageName){
       var respuesta;
         $.ajax({
           url :"getMoneyRecuRowsByRoutes/"+mes+"/"+anio+"/"+pageName,
           method:'GET',
           async : false,
           success: function(res){
            respuesta = res;
                
           }
           
        });
         return respuesta;
       
    }



    $('#btnCargardtIntroRecup').on('click', function(){
        var mes = $('#selectMesIntroRecup option:selected').val();
        var anio = $('#selectAnnoIntroRecup option:selected').val();
        pageName    = 'Recuperacion';
        var route="getMoneyRecuRowsByRoutes/"+mes+"/"+anio+"/"+pageName;
        var metodo = 'GET';
        
        asignarRutaMascaras(mes, anio);
        var ValidarTablaRecu = existenDatosEnTablaRecu(mes, anio, pageName);

        $('#btnSaveIntroRecup').text('Guardar');
        if (ValidarTablaRecu.length > 0) {
            fillDtIntroRecup(route, metodo);
            
            $('#btnSaveIntroRecup').text('Actualizar');//si hay registro label del boton es igual a "Actualizar"
            $('#btnSaveIntroRecup').val(1);

        }else{
           

            $('#btnSaveIntroRecup').text('Guardar');//si NO hay registro label del boton es igual a "Guardar"
            $('#btnSaveIntroRecup').val(0);
            obtenerRutasRecu();


        }
    });

    
    //Accion al hacer clic sobre el boton de "guardar"
    $('#btnSaveIntroRecup').on('click', function(){
        var data = new Array();

        var mes = $('#selectMesIntroRecup option:selected').val();
        var anio = $('#selectAnnoIntroRecup option:selected').val();

        var idMeta;
        var idCredito;
        var idContado;
        var table = $('#dtIntroRecup').DataTable();
        var ValidarTablaRecu = existenDatosEnTablaRecu(mes, anio);
        
        var j=0;
        //var k=0;
          
        pageName    = 'Recuperacion';
        var route="getMoneyRecuRowsByRoutes/"+mes+"/"+anio+"/"+pageName;
        var metodo = 'GET';
            
        // Obtengo datos de data table (datos de los input)
        for (var i = 0; i < table.data().count(); i++) {
            idMeta    = $('#recu_meta_'+table.cell( i, 0 ).data()).val();
            idCredito = $('#recu_credito_'+table.cell( i, 0 ).data()).val();
            idContado = $('#recu_contado_'+table.cell( i, 0 ).data()).val();
            //lleno un arreglo con los datos de la tabla de cada ruta

            if (idMeta.length == 0){
                idMeta = '0.00';
            }else{
                idMeta = idMeta.replace(/[^0-9.]/g, '');
            }

            if (idCredito.length == 0){
                idCredito = '0.00';
            }else{
                idCredito = idCredito.replace(/[^0-9.]/g, '');
            }

            if (idContado.length == 0) {
                idContado = '0.00';
             }else{
                idContado = idContado.replace(/[^0-9.]/g, '');
            }

            
            
            

            vendedores = table.cell( i, 1 ).data();
            vendedores = (vendedores.replace('<span style="text-align: left; float: left">', '')).replace('</span>','');

                data[j] = {
                    ruta: table.cell( i, 0 ).data(),
                    vendedor: vendedores,
                    Meta_recu: idMeta,
                    Recu_credito: idCredito,
                    Recu_contado: idContado,
                    fecha: anio +'-'+ mes +'-01'};
                j++;
               
          
            
        }
        
         

        if (ValidarTablaRecu.length > 0) {
           
        console.log("Actualizando");
            // Actualizo datos en campos credito y contado
            $.ajax({
                url:"actualizarMetaRecup",
                method:"POST",
                data:{data},
                success: function(res){
           
                    if (res == 1) {
                         $('#dtIntroRecup').DataTable().clear().draw();//borra las filas de las tablas en el html
                        fillDtIntroRecup(route, metodo);//Dibuja nuevamente los registros de la tabla con los cambios realizados
                        //location.reload();//ercarga pagina
                         var ValidarTablaRecu = existenDatosEnTablaRecu(mes, anio);
                        asignarRutaMascaras(mes, anio);
                        $('#btnSaveIntroRecup').text('Actualizar Registros');//si hay registro label del boton es igual a "Guardar"
                        $('#btnSaveIntroRecup').val(1);
            
       
                   
                    }
                }
            });
        }else{

        console.log("Creando");
           
            //Crear nuevos registros de recuperacion
             $.ajax({
                url:"agregarMetaRecup",
                method:"POST",
                data: {'filtered': data},
                success: function(res){
           
                    if (res == 1) {
                         $('#dtIntroRecup').DataTable().clear().draw();//elimina los registros de la tabla
                        fillDtIntroRecup(route, metodo);//Dibuja nuevamente los registros de la tabla con los cambios realizados
                        //location.reload();//ercarga pagina
                        asignarRutaMascaras(mes, anio);

                        $('#btnSaveIntroRecup').text('Actualizar Registros');//si hay registro label del boton es igual a "Guardar"
                        $('#btnSaveIntroRecup').val(1);
                   
                    }
                }
            });

        }
     
    
    });

    
       
	
</script>

