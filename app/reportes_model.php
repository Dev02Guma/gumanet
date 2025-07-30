<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class reportes_model extends Model
{
    public static function claseTerapeutica() {
        
        $sql_server = new \sql_server();

        $sql_exec = '';
        $request = Request();
        $company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;

        switch ($company_user) { 
            case '1':
                $sql_exec = " SELECT * FROM UMK_CLASIFICACION_ARTICULO ";
                break;
            case '2':
                $sql_exec = " SELECT * FROM GP_CLASIFICACION_ARTICULO ";
                break;
            case '3':
                return false;
                break;
            case '4':
                $sql_exec = " SELECT * FROM INN_CLASIFICACION_ARTICULO ";
                break;   
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }

        $query = $sql_server->fetchArray($sql_exec, SQLSRV_FETCH_ASSOC);

        if( count($query)>0 ){
            return $query;
        }

        $sql_server->close();
        return false;
    }


    public static function rutas(){
         $sql_server = new \sql_server();

        $sql_exec = '';
        $request = Request();
        $company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;

        switch ($company_user) {
            case '1':
                $sql_exec = " SELECT * FROM UMK_VENDEDORES_ACTIVO ";
                break;
            case '2':
                $sql_exec = " SELECT * FROM GP_VENDEDORES_ACTIVOS ";
                break;
            case '3':
                return false;
                break;
            case '4':
                $sql_exec = " SELECT * FROM INV_VENDEDORES_ACTIVOS ";
                break;
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }

         $query = $sql_server->fetchArray($sql_exec, SQLSRV_FETCH_ASSOC);

        if( count($query)>0 ){
            return $query;
        }

        $sql_server->close();
        return false;
    }

    public static function articulos() {
        $sql_server = new \sql_server();

        $sql_exec = '';
        $request = Request();
        $company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;

        switch ($company_user) {
            case '1':
                $sql_exec = " SELECT * FROM UMK_ARTICULOS_ACTIVOS ";
                break;
            case '2':
                $sql_exec = " SELECT * FROM GP_ARTICULOS_ACTIVOS ";
                break;
            case '3':
                return false;
                break;
            case '4':
                $sql_exec = " SELECT * FROM INN_ARTICULOS_ACTIVOS ";
                break;
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }

        $query = $sql_server->fetchArray($sql_exec, SQLSRV_FETCH_ASSOC);

        if( count($query)>0 ){
			return $query;
        }

        $sql_server->close();
        return false;
    }

    public static function Laboratorio() {
        $sql_server = new \sql_server();

        $sql_exec = '';
        $request = Request();
        $company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;

        switch ($company_user) {
            case '1':
                $sql_exec = " SELECT * FROM UMK_ARTICULOS_LABORATORIO T0 ORDER BY T0.DESCRIPCION";
                break;
            case '2':
                //$sql_exec = " SELECT * FROM GP_ARTICULOS_ACTIVOS ";
                break;
            case '3':
                return false;
                break;
            case '4':
                //$sql_exec = " SELECT * FROM INN_ARTICULOS_ACTIVOS ";
                break;
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }

        $query = $sql_server->fetchArray($sql_exec, SQLSRV_FETCH_ASSOC);

        if( count($query)>0 ){
			return $query;
        }

        $sql_server->close();
        return false;
    }

	public static function clientes() {
        $sql_server = new \sql_server();

        $sql_exec = '';
        $request = Request();
        $company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;

        switch ($company_user) {
            case '1':
                $sql_exec = " SELECT * FROM UMK_CLIENTES_ACTIVOS ";
                break;
            case '2':
                $sql_exec = " SELECT * FROM GP_CLIENTES_ACTIVOS ";
                break;
            case '3':
                return false;
                break;
            case '4':
                $sql_exec = " SELECT * FROM INN_CLIENTES_ACTIVOS ";
                break;
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }

        $query = $sql_server->fetchArray($sql_exec, SQLSRV_FETCH_ASSOC);

        if( count($query)>0 ){
			return $query;
        }

        $sql_server->close(); 
        return false;
    }

    public static function returndetalleVentas($clase, $cliente, $Labs , $articulo, $mes_, $anio, $ruta) {
        $sql_server = new \sql_server();
        $array      = array();
        $clientes   = array();
        $Articulos  = array();
        $i = 0;
        $n = 0;

        $sql_exec = '';
        $request = Request();
        $company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;

        $mes = ( $mes_=='all' )?'':$mes_;


        switch ($company_user) {
            case '1':
                $sql_exec   = "EXEC gnet_rpt_ventas_filtros_umk '".$mes."', ".$anio.", '".$clase."', '".$cliente."', '".$articulo."','".$ruta."','".$Labs."'";
                $sql_Cli    = "EXEC Umk_VentaLinea_Articulo '".$mes."', ".$anio.", '".$clase."', '".$cliente."', '".$articulo."','".$ruta."'";
                $sql_meta   = "EXEC UMK_meta_articulos '".$mes."', ".$anio.", '".$clase."', '".$cliente."', '".$articulo."'";
                break;
            case '2':
                $sql_exec   = "EXEC gnet_rpt_ventas_filtros_gup '".$mes."', ".$anio.", '".$clase."', '".$cliente."', '".$articulo."','".$ruta."','".$Labs."'";
                $sql_Cli    = "EXEC Gp_VentaLinea_Articulo '".$mes."', ".$anio.", '".$clase."', '".$cliente."', '".$articulo."','".$ruta."'";
                $sql_meta   = "EXEC Gp_meta_articulos ".$mes.", ".$anio.", '".$clase."', '".$cliente."', '".$articulo."'";
                break;
            case '3':
                return false;
                break;
            case '4':
                $sql_exec   = "EXEC gnet_rpt_ventas_filtros_inn '".$mes."', ".$anio.", '".$clase."', '".$cliente."', '".$articulo."','".$ruta."','".$Labs."'";
                $sql_Cli    = "EXEC Inv_VentaLinea_Articulo '".$mes."', ".$anio.", '".$clase."', '".$cliente."', '".$articulo."','".$ruta."'";
                $sql_meta   = "EXEC Inv_meta_articulos ".$mes.", ".$anio.", '".$clase."', '".$cliente."', '".$articulo."'";
                break;
                break; 
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }
        
        
        
        //$query2 = $sql_server->fetchArray($sql_meta, SQLSRV_FETCH_ASSOC);

        if ($company_user != 1) {
            $qCli  = $sql_server->fetchArray($sql_Cli, SQLSRV_FETCH_ASSOC);
            if( count($qCli)>0 ){
    
                foreach ($qCli as $key) {
    
                    $factura = $key['factura'];
    
                    if ( array_search($factura, array_column( $clientes, 'factura' ) ) === false) {
                        $clientes[$i]['cliente']  = $key['cliente'];
                        $clientes[$i]['nombre']  = $key['nombre'];
                        $clientes[$i]['ruta']    = $key['ruta'];
                        $clientes[$i]['factura'] = $key['factura'];
                        $clientes[$i]['fecha02']   = $key['fecha02'];
                        $clientes[$i]['total']   = array_sum(array_column(array_filter($qCli, function($item) use($factura) { return $item['factura'] == $factura; } ), 'total'));
                        $clientes[$i]['Cantidad']   = array_sum(array_column(array_filter($qCli, function($item) use($factura) { return $item['factura'] == $factura; } ), 'Cantidad'));
                        $i++;
                    }
                }
            }
        }

        $query = $sql_server->fetchArray($sql_exec, SQLSRV_FETCH_ASSOC);
        if( count($query)>0 ){
            foreach ($query as $fila) {

                $Total_Facturado        = $fila['MontoVenta'];
                $Cantidad               = $fila['Cantidad'];
                $Cantidad_bonificada    = $fila['Cantida_boni'];                
                $COSTO_PROM             = $fila['COSTO_PROM'];

                $AVG = floatval($Total_Facturado)  / (  floatval($Cantidad) + floatval($Cantidad_bonificada) );

                $Costo_total_Promedio = (floatval($Cantidad) + floatval($Cantidad_bonificada)) * floatval($COSTO_PROM);
                $Monto_Contribucion = floatval($Total_Facturado)  - floatval($Costo_total_Promedio);

                $prom_contribucion = (( $AVG - floatval($COSTO_PROM) ) / $AVG) * 100;

                $Articulos[$n]["Articulo"]           = $fila["Articulo"];
                $Articulos[$n]["Descripcion"]        = $fila["Descripcion"];            
                $Articulos[$n]["Disponible"]         = number_format($fila["CANT_DISPONIBLE"],2);
                $Articulos[$n]["TotalFacturado"]     = number_format($Total_Facturado,2);
                $Articulos[$n]["UndFacturado"]       = number_format($Cantidad, 0);
                $Articulos[$n]["UndBoni"]            = number_format($Cantidad_bonificada, 0);
                $Articulos[$n]["PrecProm"]           = number_format($AVG, 2);
                $Articulos[$n]["CostProm"]           = number_format($COSTO_PROM, 2);
                $Articulos[$n]["Contribu"]           = number_format($Monto_Contribucion, 2);
                $Articulos[$n]["MargenBruto"]        = number_format($prom_contribucion, 2);

                $n++;

            }
        }

        return $array = array(
            'objDt' => $Articulos, 
            'clientes' => $clientes
        );

        $sql_server->close();
        //return false;
    }

    public static function returnDetFactVenta($nFactura){
        $sql_server = new \sql_server();
        $Dta = array();
        

        $sql_exec = '';
        $request = Request();
        $company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;


        
        switch ($company_user) {
            case '1':
                $sql_exec = 'SELECT LINEA,FACTURA, ARTICULO, DESCRIPCION, CANTIDAD, PRECIO_UNITARIO, PRECIO_TOTAL,CANT_LIQUIDADA FROM UMK_DETALLES_FACTURAS WHERE FACTURA = '."'".$nFactura."' AND TIPO_LINEA NOT IN ('C') ORDER BY ARTICULO";
                break;
            case '2':
                $sql_exec = 'SELECT FACTURA, ARTICULO, DESCRIPCION, CANTIDAD, PRECIO_UNITARIO, PRECIO_TOTAL FROM GP_DETALLES_FACTURAS WHERE FACTURA = '."'".$nFactura."'";                
                break;
            case '3':
                return false;
                break;
            case '4':
                $sql_exec = 'SELECT FACTURA, ARTICULO, DESCRIPCION, CANTIDAD, PRECIO_UNITARIO, PRECIO_TOTAL FROM INN_DETALLES_FACTURAS WHERE FACTURA = '."'".$nFactura."'";
                break;
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }
        $query = $sql_server->fetchArray($sql_exec, SQLSRV_FETCH_ASSOC);

        if( count($query)>0 ){
            return $Dta = array('objDt' => $query);
        }

        $sql_server->close();
        return false;
    }
}
