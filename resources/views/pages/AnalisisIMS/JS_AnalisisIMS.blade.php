<script>
    $(document).ready(function() {
        
        fullScreen();
        getDetallesSKUCliente();
      
        $("#id_search_reorder").on('keyup', function() {
            var searchTerm = $(this).val().toLowerCase();
            $('#tbl_Ims').DataTable().search(searchTerm).draw();
        });

        $( "#select_rows").change(function() {
            var table = $('#tbl_Ims').DataTable();
            table.page.len(this.value).draw();
        });

    });

    let topStart_custom = document.createElement('div');
    topStart_custom.setAttribute('class', 'col-12 ');
    topStart_custom.innerHTML = `
    <div class="row">
        <div class="col-sm-10">	
            <div class="input-group"> 
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></i></span>
                </div>
                <input type="text" id="id_search_reorder" class="form-control" placeholder="Buscar..." aria-label="Username" aria-describedby="basic-addon1">
            </div>
        </div>
        <div class="col-sm-2 col-md-2">
            <select class="custom-select" id="select_rows">
                <option value="7" selected>7</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="100">100</option>
                <option value="-1">Todo</option>
            </select>
        </div>
    </div>`;

    function tbl_analisis_ims(Dt) {
       // $('#tbl_Ims').DataTable().clear().destroy();
         
        new $("#tbl_Ims").DataTable({
            data: Dt,
            destroy: true,
            buttons: [{extend: 'excelHtml5'}],
            order: [],
            scrollCollapse: true,
            //scrollX: true,
            columns: [
                { data: "ARTICULO", title: "ARTICULO" },
                { data: "DESCRIPCION", title: "DESCRIPCION" },
                { data: "FACTOR_EMPAQUE", title: "FACTOR_EMPAQUE", render: $.fn.dataTable.render.number(',', '.', 2, '') },
                { data: "COUNT_COMPETIDORES", title: "COMPETIDORES" },
                { data: "CRECI_23_24", title: "CRECI_23_24", class: "text-right", render: $.fn.dataTable.render.number(',', '.', 2, '') },               
                { data: "DIF", title: "DIF", class: "text-right", render: $.fn.dataTable.render.number(',', '.', 2, '') },               
                { data: "DIF_TOP1", title: "DIF TOP1" , class: "text-right", render: $.fn.dataTable.render.number(',', '.', 2, '')},
                { data: "DIF_TOP2", title: "DIF TOP2" , class: "text-right", render: $.fn.dataTable.render.number(',', '.', 2, '')},
                { data: "DIF_TOP3", title: "DIF TOP3" , class: "text-right", render: $.fn.dataTable.render.number(',', '.', 2, '')},
                { data: "MARKET_SHARE_UMK_23", title: "MARKET_SHARE_UMK_23" , class: "text-right", render: $.fn.dataTable.render.number(',', '.', 2, '')},
                { data: "MARKET_SHARE_UMK_24", title: "MARKET_SHARE_UMK_24" , class: "text-right", render: $.fn.dataTable.render.number(',', '.', 2, '')},
                { data: "PACKS_CANT1", title: "PACKS_CANT1" , class: "text-right", render: $.fn.dataTable.render.number(',', '.', 0, '')},
                { data: "PACKS_CANT2", title: "PACKS_CANT2" , class: "text-right", render: $.fn.dataTable.render.number(',', '.', 0, '')},
                { data: "PACKS_CANT3", title: "PACKS_CANT3" , class: "text-right", render: $.fn.dataTable.render.number(',', '.', 0, '')},
                { data: "PACKS_CANT_DIF1_24", title: "PACKS_CANT_DIF1_24", class: "text-right", render: $.fn.dataTable.render.number(',', '.', 2, '') },               
                { data: "PACKS_CANT_DIF2_24", title: "PACKS_CANT_DIF2_24", class: "text-right", render: $.fn.dataTable.render.number(',', '.', 2, '') },               
                { data: "PACKS_CANT_DIF3_24", title: "PACKS_CANT_DIF3_24", class: "text-right", render: $.fn.dataTable.render.number(',', '.', 2, '') },               
                { data: "PACKS_MANU1", title: "PACKS_MANU1" },               
                { data: "PACKS_MANU2", title: "PACKS_MANU2" },               
                { data: "PACKS_MANU3", title: "PACKS_MANU3" },               
                { data: "PACKS_TOTAL_EQV_IMS_23", title: "PACKS_TOTAL_EQV_IMS_23", class: "text-right", render: $.fn.dataTable.render.number(',', '.', 0, '') },               
                { data: "PACKS_TOTAL_EQV_IMS_24", title: "PACKS_TOTAL_EQV_IMS_24", class: "text-right", render: $.fn.dataTable.render.number(',', '.', 0, '') },               
                { data: "PACKS_TOTAL_UMK23", title: "PACKS_TOTAL_UMK23", class: "text-right", render: $.fn.dataTable.render.number(',', '.', 0, '') },               
                { data: "PACKS_TOTAL_UMK24", title: "PACKS_TOTAL_UMK24", class: "text-right", render: $.fn.dataTable.render.number(',', '.', 0, '') },               
                { data: "PRECIO_PROM_IMS_2024", title: "PRECIO_PROM_IMS_2024", class: "text-right", render: $.fn.dataTable.render.number(',', '.', 2, '') },               
                { data: "TOP1_AVG_PRICE", title: "TOP1_AVG_PRICE", class: "text-right", render: $.fn.dataTable.render.number(',', '.', 2, '') },               
                { data: "TOP1_MANU_DESC", title: "TOP1_MANU_DESC" },
                { data: "TOP1_CANT", title: "TOP1_CANT", render: $.fn.dataTable.render.number(',', '.', 2, '') },
                { data: "TOP1_PRICE", title: "TOP1_PRICE", class: "text-right", render: $.fn.dataTable.render.number(',', '.', 2, '') },               
                { data: "TOP2_AVG_PRICE", title: "TOP2_AVG_PRICE", class: "text-right", render: $.fn.dataTable.render.number(',', '.', 2, '') },               
                { data: "TOP2_MANU_DESC", title: "TOP2_MANU_DESC" },               
                { data: "TOP2_CANT", title: "TOP2_CANT", render: $.fn.dataTable.render.number(',', '.', 2, '') },
                { data: "TOP2_PRICE", title: "TOP2_PRICE", class: "text-right", render: $.fn.dataTable.render.number(',', '.', 2, '') },               
                { data: "TOP3_AVG_PRICE", title: "TOP3_AVG_PRICE", class: "text-right", render: $.fn.dataTable.render.number(',', '.', 2, '') },               
                { data: "TOP3_MANU_DESC", title: "TOP3_MANU_DESC" },               
                { data: "TOP3_CANT", title: "TOP3_CANT", render: $.fn.dataTable.render.number(',', '.', 2, '') },
                { data: "TOP3_PRICE", title: "TOP3_PRICE", class: "text-right", render: $.fn.dataTable.render.number(',', '.', 2, '') },               
                { data: "VAL_US_2024", title: "VAL_US_2024", class: "text-right", render: $.fn.dataTable.render.number(',', '.', 2, '') },               
            ],
            "columnDefs": [
                { "width": "300px", "targets": [ 1 ] }
            ],
            pageLength: 7,
            bLengthChange: false,
            searching: true,
            layout: {
                topStart: null,
                bottom: 'paging',
                bottomStart: null,
                bottomEnd: null,     
                topStart : topStart_custom
            },            
            
        });
        $('#tbl_Ims thead tr').addClass('bg-umk text-white text-center');
        $("#tbl_Ims_filter").hide();
    }

     async function getDetallesSKUCliente() {
      try {
        const response = await fetch('getDataAnalisisIMS');
        const result = await response.json();

        tbl_analisis_ims(result);
      } catch (error) {
        console.error(error);
      }
    }

</script>