<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('/infraestructura','infraestructura_controller@home');
Route::get('/getProyects','infraestructura_controller@getProyects');
Route::post('/getTasksProjects','infraestructura_controller@getTasksProjects');


//RUTAS MENU
Route::get('/Inventario','inventario_controller@index');
Route::get('/Metas','metas_controller@index');
Route::get('/Usuario','usuario_controller@index');
Route::get('/Reportes','reportes_controller@index');
Route::get('/Recuperacion','recuperacion_controller@index');
Route::get('/Saldos','saldos_controller@index');
Route::get('/Proyecciones','proyecciones_controller@index');
Route::get('/InteligenciaMercado','inteligenciaMercado_controller@index');
Route::get('/InvTotalizado','inventario_controller@inventarioTotalizado');
Route::get('/VtsProyectos','ventasProyectos_controller@index');
Route::get('/MinutasCorporativas','minutasCorp_controller@index');
Route::get('/Menus','menus_controller@index');
Route::get('/Roles','rol@index');
Route::get('/recuProyectos','recupProyectos_controller@index');
Route::get('/ordenesCompra', 'ordenesCompra_controller@index');
Route::get('/DetalleOrden', 'DetalleOrdenController@index');
Route::get('/Comiciones', 'ComisionController@index')->name('/Comiciones');
Route::get('/getDataComiciones', 'ComisionController@getDataComiciones')->name('/getDataComiciones');
Route::get('/getHistoryItem', 'ComisionController@getHistoryItem')->name('/getHistoryItem');

//RUTAS LOGIN
Route::get('/','Auth\LoginController@showLoginForm');//pagina login
Auth::routes();//dentro de la funcion routes() se encunetran todas las rutas para login del Auth "Vendor/laravel/src/illuminate/routing/router.php"
Route::get('/Dashboard','dashboard_controller@index')->name('Dashboard');
Route::get('/getCompanies','Auth\LoginController@getCompanies');
//Route::get('password/reset/{token}', 'resetPassword_Controller@index')->name('pass.reset','{token}');

//RUTA RESET PASS
Route::get('/formReset','resetPass_controller@index')->name('formReset');
Route::post('/resetPass','resetPass_controller@resetPass')->name('resetPass');


//RUTAS USUARIO
Route::get('/usuarios','usuario_controller@getUsuario');
Route::get('/usuario/{id}/companies','usuario_controller@getCompaniesByUserId');
Route::get('/company/{id}/usuarios','usuario_controller@getUsersByCompanyId');
Route::post('/editUser','usuario_controller@editUser');
Route::post('/deleteUser','usuario_controller@deleteUser');
Route::post('/changeUserStatus','usuario_controller@changeUserStatus');
Route::get('/role','usuario_controller@getRole');

//RUTAS INVENTARIO
Route::get('/articulos','inventario_controller@getArticulos');
Route::get('/objBodega/{articulo}','inventario_controller@getBodegaInventario');
Route::get('/objPrecios/{articulo}','inventario_controller@getPreciosArticulos');
Route::get('/objCostos/{articulo}','inventario_controller@getCostosArticulos');
Route::get('/objMargen/{articulo}','inventario_controller@getMargenArticulos');
Route::get('/objOtros/{articulo}','inventario_controller@getOtrosArticulos');
Route::get('/objVineta/{articulo}','inventario_controller@getVineta');
Route::get('/objBonificado/{articulo}','inventario_controller@getArtBonificados');
Route::get('/objIndicadores/{articulo}','inventario_controller@objIndicadores');

Route::post('/transacciones','inventario_controller@transaccionesDetalle');
Route::post('/lotes','inventario_controller@getLotesArticulo');

Route::post('/getLotes','inventario_controller@getLotes');
Route::get('/liqMeses/{valor}','inventario_controller@liquidacionMeses');
Route::get('/desInventario/{tipo}/{valor}', 'inventario_controller@descargarInventario');
Route::get('/invCompleto', 'inventario_controller@inventarioCompleto');
Route::get('/invTotalizadoDT', 'inventario_controller@inventarioCompletoTable');
Route::get('/desInvTotal2', 'inventario_controller@descargarInventarioCompleto');
Route::get('/invenVencidos', 'inventario_controller@invenVencidos');
Route::post('/getAllBodegas','inventario_controller@getAllBodegas');


//RUTAS INVENTARIO TOTALIZADO 
Route::get('/invTotalizado','inventario_controller@getInventarioTotalizado');
Route::get('/desInvTotal','inventario_controller@descargarInventarioCompleto');

//RUTAS METAS
Route::post('/export_meta_from_exl','metas_controller@exportMetaFromExl');
Route::post('/export_meta_from_exl_venta','metas_controller@exportMetaFromExlVenta');
Route::post('/get_tmp_exl_data','metas_controller@getTmpExlData');
Route::get('/add_data_meta','metas_controller@add_data_meta');
Route::post('/calc_and_add_unidad_meta','metas_controller@calcAddUnidadMeta');
Route::get('/truncate_tmp_exl_tbl','metas_controller@truncate_tmp_exl_tbl');
Route::post('/get_historial_meta','metas_controller@getHistorialMeta');
Route::post('/existe_Fecha_Meta','metas_controller@existeFechaMeta');
Route::post('/existe_Fecha_Meta_venta','metas_controller@existeFechaMetaVenta');
Route::post('/addDataRecuToDB','metas_controller@addDataRecuToDB');
Route::post('/getHistoriaMetaRecu','metas_controller@getHistoriaMetaRecu');

//RUTAS DETALLE DE VENTAS
Route::get('/detalles/{tipo}/{mes}/{anio}/{cliente}/{articulo}/{ruta}','dashboard_controller@getDetalleVentas');
Route::get('/detallesdia/{dia}/{mes}/{anio}/{seg}','dashboard_controller@getDetalleVentasDia');
Route::get('/detallesVentasRuta/{mes}/{anio}/{ruta}','dashboard_controller@getDetalleVentasXRuta');
Route::get('/detallesVentasRutaDia/{dia}/{mes}/{anio}/{ruta}','dashboard_controller@get_Vta_Ruta_dia');
Route::get('/detallesTodosItems/{dia}/{mes}/{anio}/{segmento}','dashboard_controller@get_Vta_all_items');
Route::get('/excelAllTop10/{dia}/{mes}/{anio}/{segmento}','dashboard_controller@get_all_top');
Route::get('/graficaSegmento/{mes}/{anio}/{bolson}/{Segmento}','dashboard_controller@GetTop10Productos');
Route::get('/graficaSegmentoCL/{mes}/{anio}/{bolson}/{Segmento}','dashboard_controller@getTop10Clientes');
//Route::get('/ruta/{mes}/{anio}','dashboard_controller@getTotalRutaXVentas');
Route::get('/unidadxProd/{mes}/{anio}','dashboard_controller@getTotalUnidadesXRutaXVentas');
Route::get('/ClientesNoFacturados/{mes}/{anio}','dashboard_controller@ClientesNoFacturados');
Route::get('/ArticuloNoFacturado/{mes}/{anio}','dashboard_controller@ArticuloNoFacturado');
Route::get('/getClientesSinComprar/{mes}/{anio}','dashboard_controller@getClientesSinComprar');





//RUTAS GRAFICAS DASHBOARDS
Route::get('/dataGraf/{mes}/{anio}/{xbolsones}','dashboard_controller@getDataGraficas');
Route::get('/Grafselect/{mes}/{anio}/{xbolsones}/{segmento}','dashboard_controller@getDataGrafSelect');

Route::get('/detailsAllCls/{mes}/{anio}/{categoria}/{bolson}','dashboard_controller@getAllClientsByCategory');


Route::get('/dataVentasMens/{xbolsones}','dashboard_controller@getVentasMensuales');


Route::get('/dtaComportamientoAnuales/{xbolsones}','dashboard_controller@getComportamiento');

Route::get('/dataRealVtsMensuales/{xbolsones}/{segmentos}','dashboard_controller@getRealVentasMensuales');
Route::get('/top10Cls','dashboard_controller@getTop10Clientes');
Route::get('/valBodegas','dashboard_controller@getValBodegas');
Route::post('/dataCate', 'dashboard_controller@ventaXCategorias');
Route::get('/getRecuRowsByRoutes/{mes}/{anio}/{pageName}','dashboard_controller@getRecuRowsByRoutes');

//RUTAS REPORTES VENTAS
Route::post('/ventasDetalle','reportes_controller@detalleVentas');
Route::post('/getDetFactVenta','reportes_controller@getDetFactVenta');

//RUTAS RECUPERACION
Route::get('/getMoneyRecuRowsByRoutes/{mes}/{anio}/{pageName}','recuperacion_controller@getMoneyRecuRowsByRoutes');
Route::post('/agregarMetaRecup','recuperacion_controller@agregarMetaRecup');
Route::post('/actualizarMetaRecup','recuperacion_controller@actualizarMetaRecup');
Route::get('/obtenerRutasRecu/{mes}/{anio}','recuperacion_controller@obtenerRutasRecu');

//RUTAS SALDOS
Route::get('/saldoAlls','saldos_controller@saldosAll');
Route::post('/saldoxRuta','saldos_controller@saldosXRuta');

//RUTAS PROYECCIONES
Route::post('dataProyeccion','proyecciones_controller@dataProyeccionXTipo');
Route::post('artProyectado', 'proyecciones_controller@dataProyeccionXArticulo');

//RUTAS COMENTARIOS
Route::post('/paginateDataSearch', 'inteligenciaMercado_controller@searchComentarios');
Route::post('/dowloadComents', 'inteligenciaMercado_controller@descargarComentarios');
Route::get('countim', 'inteligenciaMercado_controller@countim');

//RUTAS VENTAS POR PROYECTOS
Route::get('/dataVTS','ventasProyectos_controller@comparateDateVentas');

//RUTAS MINUTAS CORPORATIVAS
Route::post('/paginateDataSearchBlogs', 'minutasCorp_controller@searchBlogs');
Route::get('/minutaCU', 'minutasCorp_controller@createUpdateMinuta');
Route::post('saveMinuta', 'minutasCorp_controller@guardarMinuta');
Route::get('minuta/{idMinuta}/{action}', 'minutasCorp_controller@getDataMinuta');
Route::get('deleteMinuta/{idMinuta}', 'minutasCorp_controller@deleteMinuta');
Route::post('updateMinuta', 'minutasCorp_controller@actulizarMinutaCorp');

//RUTAS MENUS
Route::post('menu-rol', 'menus_controller@guardar');
Route::get('menu/crear', 'menus_controller@crear')->name('menu/crear');
Route::post('menu/guardar', 'menus_controller@guardarNuevoMenu')->name('menu/guardar');

//RUTAS ROLES
Route::get('rol/crear', 'rol@crear')->name('rol/crear');
Route::post('rol/guardar', 'rol@guardar')->name('rol/guardar');

//RUTAS RECUPERACION POR PROYECTOS
Route::get('dataRECUP', 'recupProyectos_controller@comparateDateRecup');

//RUTAS ORDENES DE COMPRA
Route::get('ordenes', 'ordenesCompra_controller@getDataOrdenesCompra');
Route::post('/lineasOrden','ordenesCompra_controller@getDetalleOrdenCompra');

//TODAS LAS RUTAS QUE TENGAN QUE VER CON VIÑETAS
Route::get('vineta', 'vinneta_controller@index');
Route::get('getVinnetas', 'vinneta_controller@getVinnetas');
Route::get('getPagadoRuta', 'vinneta_controller@getPagadoRuta');
Route::get('getVinnetasResumen', 'vinneta_controller@getVinnetasResumen');

Route::get('liqvineta', 'vinetaliq_controller@index');
Route::get('getSolicitudes', 'vinetaliq_controller@getSolicitudes');
Route::get('getLiquidaciones', 'vinetaliq_controller@getLiquidaciones');

Route::post('PushLiq', 'vinetaliq_controller@pushliq');
Route::post('cancelarliq', 'vinetaliq_controller@cancelarliq');
Route::post('Deleteliq', 'vinetaliq_controller@Deleteliq');
Route::post('Anular_Vineta', 'vinetaliq_controller@AnularVineta');
Route::post('HistorialFactura', 'vinneta_controller@getHistorialFactura');

Route::post('pagado', 'vinneta_controller@getpagado');

Route::get('resumen', 'vinetaliq_controller@resumenpdf');
Route::get('rePrint', 'vinetaliq_controller@rePrint');

Route::get('clean', 'vinetaliq_controller@getClear');

//RUTAS PARA EL DETALLE DE ORDENES
Route::get('/DetalleOrdenesDT', 'DetalleOrdenController@getDetalleOrdenes');  
Route::get('/getMateriaPrima/{numOrden}','DetalleOrdenController@getMateriaPrima');
Route::get('/getMOD/{numOrden}','DetalleOrdenController@getMOD');
Route::get('/getQuimicos/{numOrden}','DetalleOrdenController@getQuimicos');
Route::get('/getSubCostos/{numOrden}','DetalleOrdenController@getSubCostos');
Route::get('/getOtrosConsumos/{numOrden}','DetalleOrdenController@getOtrosConsumos');
Route::get('/getDetailSumary/{numOrden}','DetalleOrdenController@getDetailSumary');
Route::get('/getHrasProducidas/{numOrden}','DetalleOrdenController@getHrasProducidas');
Route::get('/getData','DetalleOrdenController@getData');

//RUTAS PARA LOS RECIBOS
Route::get('recibos', 'recibos_controller@index');  
Route::get('getRecibos', 'recibos_controller@getRecibos');

Route::get('Cartera', 'recibos_controller@getReporte');  
Route::get('getCartera', 'recibos_controller@getCartera');
Route::get('getOneRecibos', 'recibos_controller@getOneRecibos');

Route::get('getAttachFile', 'recibos_controller@getAttachFile');
Route::post('push_recibo', 'recibos_controller@push_recibo');
Route::post('push_verificado', 'recibos_controller@push_verificado');
Route::get('print_resumen', 'recibos_controller@print_resumen');


// RUTAS PARA PROMOCIONES DE INNOVA
Route::get('promocion_Vueno', 'promocion_controller@index');
Route::get('getPromocion', 'promocion_controller@getPromocion');
Route::post('getHistorialFactura','promocion_controller@getHistorialFactura');
Route::get('getResumen', 'promocion_controller@getResumen');

// RUTAS PARA INFORMACION DE FACTURA DE EXPORTACIONES
Route::get('exportacion', 'exportacion_controller@index');
Route::get('getVentasExportacion', 'exportacion_controller@getVentasExportacion');
Route::get('/dtaVentaExportacion/{xbolsones}/{segmentos}','dashboard_controller@getVentasExportacion');
Route::post('AnularFactura', 'exportacion_controller@AnularFactura')->name('AnularFactura');

Route::get('/ArticuloDetalles/{articulo}/{unidad}','inventario_controller@getArticuloDetalles')->name('ArticuloDetalles');;

Route::get('Promocion', 'PromocionController@getPromocion')->name('Promocion');
Route::get('getPromoMes', 'PromocionController@getPromoMes')->name('getPromoMes');

Route::get('inventarioInnova', 'InnovaController@inventarioInnova')->name('inventarioInnova');
Route::get('getKerdex', 'InnovaController@getKerdex')->name('getKerdex');
Route::get('getResumenKardex', 'InnovaController@getResumenKardex')->name('getRasumenKardex');
Route::get('getMateriaPrima', 'InnovaController@getMateriaPrima')->name('getMateriaPrima');


Route::get('getStatsInn', 'InnovaController@getStatsInn')->name('getStatsInn');
Route::get('saveInnStat', 'InnovaController@saveInnStat')->name('saveInnStat');

Route::post('getSaleCadena', 'dashboard_controller@getSaleCadena');
Route::post('getSaleCadenaDetalle', 'dashboard_controller@getSaleCadenaDetalle')->name('getSaleCadenaDetalle');
Route::post('getSaleInstitucion', 'dashboard_controller@getSaleInstitucion');
Route::post('getSaleDetalleInsta', 'dashboard_controller@getSaleDetalleInsta')->name('getSaleDetalleInsta');


// TODAS LAS RUTAS DEL REORDER POINT
Route::get('ReporderPoint', 'ReOrderPointController@ReOrderPoint')->name('ReporderPoint');
Route::get('getData', 'ReOrderPointController@getData')->name('getData');
Route::get('dtGraf/{articulo}','ReOrderPointController@getDataGrafica')->name('dtGraf/{articulo}');


// ROUTER DE PROYECTO 71 & 89
Route::get('Presupuesto', 'BudgetController@ViewBudget')->name('Presupuesto');
Route::get('dtProyect', 'BudgetController@dtProyect')->name('dtProyect');
Route::get('dtArticulo', 'BudgetController@dtArticulo')->name('dtArticulo');


