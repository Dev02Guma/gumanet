<?php

namespace App\Http\Controllers;

use App\Models;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Company;
use App\solicitudes_model;
use App\recibos_model;
use App\reportes_model;
use Illuminate\Support\Facades\DB;
use GPDF;
use PDF;
use App\app_onesignal;
use Illuminate\Support\Facades\Storage;

class recibos_controller extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }

    public function getClear(Request $request){

        /*tbl_liquidacion_fondo_vineta::truncate();
        
        tbl_liquidacion_detalle::truncate();

        solicitudes_model::truncate();

        DB::connection('sqlsrv')->table('DESARROLLO.dbo.tbl_vineta_liquidadas')->delete();*/

    }

    public function print_resumen(Request $request) {

        $vlLinea        = 0; 
        $recibido       = 0; 
        $Lineas         = [];


        $aRutas         = reportes_model::rutas();
        $resumen        = json_decode(recibos_controller::getRecibos($request)->content(), true);        
        $Ruta           = $request->input('RU');
        $found_key      = array_search($Ruta, array_column($aRutas, 'VENDEDOR'));
        $Ruta_Name      = $aRutas[$found_key]['NOMBRE'];
        $Fecha_generado = date('Y-m-d H:i:s');        
        $Nota           = $request->input('nota');

        
        
        $data = [
            'Ejecutivo'   =>  $Ruta_Name,
            'Fecha'       =>  $Fecha_generado,
            'Ruta'        =>  $Ruta,
            'Nota'        =>  $Nota
        ];

        foreach($resumen as $key){     
            $suma       = 0;  
            $vlLinea    = 0;   

            $vlLinea += preg_replace('/[^0-9-.]+/', '', $key['TOTAL']);
            $recibido += preg_replace('/[^0-9-.]+/', '', $key['TOTAL']);

            foreach($key['DETALLES'] as $dt){
                $suma++;
            }

            $Lineas[] = [
                'id'            => 0,
                'Fecha'         => $key['FECHA'],
                'Recibo'        => $key['RECIBO'],
                'cliente_name'  => $key['NOMBRE_CLIENTE'],
                'cliente_cod'   => $key['CLIENTE'],
                'Concepto'      => "Pago Viñeta ( ".$suma." )",
                'Total'         => $vlLinea,
                'created_at'    => date('Y-m-d H:i:s')
            ];
            
        }

        //return view('pages.recibo_print', compact('data','resumen'));

        $pdf = PDF::loadView('pages.recibo_print', compact('data','resumen'));
        return $pdf->download('Resumen.pdf');
        
    }


    public function rePrint(Request $request) {

        
        $IdLiquidacion = $request->input('Id') ;

        $Lineas             = [];
        $row_Liq            = tbl_liquidacion_fondo_vineta::where('Id',$IdLiquidacion)->get();	
        $row_Liq_detalles   = tbl_liquidacion_detalle::where('id',$IdLiquidacion)->get()->toArray();;	

        
        $Ruta           = $row_Liq[0]['Ruta'];
        $Ruta_Name      = $row_Liq[0]['Ruta_name'];
        $Fecha_generado = $row_Liq[0]['Fecha'];

        $FondoInicial   = $row_Liq[0]['Fondo_inicial'];
        $Nota           = $row_Liq[0]['Nota'];

        $IdLiquidacion = $this->addZeros($IdLiquidacion);


        $data = [
            'Ejecutivo'   =>  $Ruta_Name,
            'Fecha'       =>  $Fecha_generado,
            'Ruta'        =>  $Ruta,
            'Fondo'       =>  $FondoInicial,
            'Nota'        =>  $Nota,
            'IdLiq'       =>  $IdLiquidacion
        ];

        $resumen = $row_Liq_detalles;
        
        //return view('pages.reprintPDF', compact('data','resumen'));

        $pdf = PDF::loadView('pages.reprintPDF', compact('data','resumen'));
        return $pdf->download('Resumen.pdf');
        
    }

    public function index() {
        $this->agregarDatosASession();

        $clientes   = reportes_model::clientes();
        $rutas      = reportes_model::rutas();

        $data = [
            'name' =>  'GUMA@NET',
            'page' => 'Ventas'
        ];
        
        return view('pages.recibos', compact('data', 'clientes','rutas'));
    }

    public function getReporte() {
        $this->agregarDatosASession();

        $rutas      = reportes_model::rutas();

        $data = [
            'name' =>  'GUMA@NET',
            'page' => 'Ventas'
        ];
        
        return view('pages.cartera', compact('data','rutas'));
    }

    public function agregarDatosASession() {
        $request = Request();
        $ApplicationVersion = new \git_version();
        $company = Company::where('id',$request->session()->get('company_id'))->first();
        $request->session()->put('ApplicationVersion', $ApplicationVersion::get());
        $request->session()->put('companyName', $company->nombre);
    }
    public function getAttachFile(Request $request) {

        $data = array();
        $i=0;
        $Recibo = $request->input('iRecibo');

        if (!$Recibo) {
            return response()->json(false);
        }

        $query = DB::table('tbl_order_recibo_adjuntos')->where('id_recibo', $Recibo)->get();

        //$obj = $query->get();


        foreach ($query as $qR => $key) {

           
            $data[$i]['IMAGEN'] = Storage::Disk('s3')->temporaryUrl('Adjuntos-Recibos/'.$key->Nombre_imagen, now()->addMinutes(5));

            $i++;

        }  

        return response()->json($data);

    }
    public static function getRecibos(Request $request) {
        

        $from   = $request->input('f1').' 00:00:00';
        $to     = $request->input('f2').' 23:59:59';
        
        $Ruta   = $request->input('RU');
        $Clie   = $request->input('CL');
        $Stat   = $request->input('St');

        $Role = $request->session()->get('user_role');

        $i=0;

        if (!$from) {
            return response()->json(false);
        }

        $data = array();

        $query = DB::table('tbl_order_recibo')->whereBetween('fecha_recibo', [$from, $to])->whereNotIn('status', array(3));

        if($Ruta != '') {
            $query->where('ruta', $Ruta);
        }
        
        if($Clie != '') {
            $query->where('cod_cliente', 'like', '%'.$Clie.'%');
        }

        if($Stat != '') {
            $query->whereIn('status', array($Stat));
        }

        $obj = $query->orderBy('id', 'ASC')->get();

    
        foreach ($obj as $qR => $key) {

            

            $arrDetalles = array();


            $data[$i]["DETALLE"]        = '<a id="exp_more" class="exp_more" href="#!"><i class="material-icons expan_more">expand_more</i></a>';
            $data[$i]['ID']             = $key->id;
            $data[$i]['STATUS']         = $key->status;
            $data[$i]['VENDEDOR']       = $key->ruta;
            $data[$i]['CLIENTE']        = substr(TRIM($key->cod_cliente),0,-1);;
            $data[$i]['NOMBRE_CLIENTE'] = $key->name_cliente;
            $data[$i]['FECHA']          = date('d/m/Y', strtotime($key->fecha_recibo));       
            $data[$i]['TOTAL']          = $key->order_total;
            $data[$i]['ADBJ']           = recibos_controller::isAdjunto($key->id);
            
            $data[$i]['RECIBO']         = $key->recibo;            
            $data[$i]['COMMENT']        = $key->comment;
            $data[$i]['COMMENT_ANUL']   = $key->comment_anul;
            $data[$i]['STATUS']         = recibos_controller::getStatus($key->status);


            $OrdenList  = $key->order_list;
            $Lineas     = explode("],", $OrdenList);
            $cLineas    = count($Lineas) - 1;

            
            

            for ($l=0; $l < $cLineas ; $l++){
                
                $Lineas_detalles     = explode(";", $Lineas[$l]);

                $arrDetalles[$l]['FACTURA']         = str_replace('[', '', $Lineas_detalles[0]);
                $arrDetalles[$l]['VALORFACTURA']    = $Lineas_detalles[1];
                $arrDetalles[$l]['NOTACREDITO']     = $Lineas_detalles[2];
                $arrDetalles[$l]['RETENCION']       = $Lineas_detalles[3];
                $arrDetalles[$l]['DESCUENTO']       = $Lineas_detalles[4];
                $arrDetalles[$l]['VALORRECIBIDO']   = $Lineas_detalles[5];
                $arrDetalles[$l]['TIPO']            =  (!isset($Lineas_detalles[8])) ? "N/D" : $Lineas_detalles[8] ;

                
            }

            $data[$i]['DETALLES']       = $arrDetalles;

            if ($Role == 8) {
                if ($key->status==0) {
                    $data[$i]["BOTONES"]    = '<div class="alert alert-success" role="alert">Pendiente.</div>';
                } 
            } else {
                $data[$i]["BOTONES"]    = ' <button type="button" class="btn btn-outline-secondary"  onClick="Aprobado('.$key->id.')">
                                                <i class="material-icons text-green mt-1"  style="font-size: 20px">done</i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger"  onClick="attach_file('.$key->id.')">
                                                <i class="material-icons text-red mt-1"  style="font-size: 20px">attach_file</i>
                                            </button>                                        
                                            ';
                
            }

            if($key->status==1) {

                /*$data[$i]["BOTONES"]        = '<button type="button" class="btn btn-outline-secondary"  onClick="Verificado('.$key->id.')">
                                                    <i class="material-icons text-green mt-1"  style="font-size: 20px">done_all</i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger"  onClick="attach_file('.$key->id.')">
                                                    <i class="material-icons text-red mt-1"  style="font-size: 20px">attach_file</i>
                                                </button>                                        
                                            ';*/
                $data[$i]["BOTONES"]        =  '<button type="button" class="btn btn-outline-success" >
                                                    <i class="material-icons text-green mt-1"  style="font-size: 20px">done</i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger"  onClick="attach_file('.$key->id.')">
                                                    <i class="material-icons text-red mt-1"  style="font-size: 20px">attach_file</i>
                                                </button>';

            }else if($key->status==2){
                $data[$i]["BOTONES"]        =  '<button type="button" class="btn btn-outline-success" >
                                                    <i class="material-icons text-green mt-1"  style="font-size: 20px">done</i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger"  onClick="attach_file('.$key->id.')">
                                                    <i class="material-icons text-red mt-1"  style="font-size: 20px">attach_file</i>
                                                </button>';
            } else if($key->status==3){
                $data[$i]["BOTONES"]        = '';
            }else if($key->status==4){
                $data[$i]['NOMBRE_CLIENTE'] = $key->comment;
                $data[$i]["BOTONES"]        = '';
            }
            
            
            $i++;
        }
        
        return response()->json($data);

    }

    public static function getOneRecibos(Request $request) {
        
        $Id   = $request->input('id_recibo');
        $data = array();
        $i=0;

        $query = DB::table('tbl_order_recibo')->where('id', $Id)->get();
        $rutas      = reportes_model::rutas();
     
        
        foreach ($query as $qR => $key) {
            $arrDetalles = array();

            $found_key = array_search($key->ruta, array_column($rutas, 'VENDEDOR'));

            $Nombre = ($found_key == false) ? 'N/D' : $rutas[$found_key]['NOMBRE'];

            $data[$i]['VENDEDOR']       = $key->ruta;
            $data[$i]['NOMBREV']        = $Nombre;
            $data[$i]['FECHA']          = date('d/m/Y', strtotime($key->fecha_recibo));       
            $data[$i]['TOTAL']          = $key->order_total;
            $data[$i]['COMMENT']        = $key->comment;

            $OrdenList  = $key->order_list;
            $Lineas     = explode("],", $OrdenList);
            $cLineas    = count($Lineas) - 1;

            for ($l=0; $l < $cLineas ; $l++){
                
                $Lineas_detalles     = explode(";", $Lineas[$l]);

                $arrDetalles[$l]['FACTURA']         = str_replace('[', '', $Lineas_detalles[0]);
                $arrDetalles[$l]['VALORFACTURA']    = number_format($Lineas_detalles[1],2);
                $arrDetalles[$l]['NOTACREDITO']     = number_format($Lineas_detalles[2],2);
                $arrDetalles[$l]['RETENCION']       = number_format($Lineas_detalles[3],2);
                $arrDetalles[$l]['DESCUENTO']       = number_format($Lineas_detalles[4],2);
                $arrDetalles[$l]['VALORRECIBIDO']   = number_format($Lineas_detalles[5],2);
                $arrDetalles[$l]['TIPO']            =  (!isset($Lineas_detalles[8])) ? "N/D" : $Lineas_detalles[8] ;

                
            }
            $data[$i]['DETALLES']       = $arrDetalles;
            $i++;

        }
        
        return response()->json($data);

    }

    public static function getCartera(Request $request) 
    {
        $data = array();
        $i=0;

        $GrupoB = array('F18', 'F19','F21','F22', 'F23');


        $from   = $request->input('f1').' 00:00:00';
        $to     = $request->input('f2').' 23:59:59';
        
        $Ruta   = $request->input('RU');
        $Stat   = $request->input('St');

        $Role = $request->session()->get('user_role');

        $rutas      = reportes_model::rutas();

      

        if (!$from) {
           return response()->json(false);
        }

        $query = DB::table('tbl_order_recibo')
        ->select(DB::raw("
            ruta ,		
            COUNT(CASE WHEN status = '0' THEN 1 ELSE NULL END) as count_ingress,
            COUNT(CASE WHEN status = '1' THEN 1 ELSE NULL END) as count_process,
            COUNT(CASE WHEN status = '4' THEN 1 ELSE NULL END) as count_anulado,
            COUNT(*) count_total,
            SUM(CASE WHEN status = '0' THEN CleanAmount ( order_total ) else 0 end) as sum_ingress,		
            SUM(CASE WHEN status = '1' THEN CleanAmount ( order_total ) else 0 end) as sum_process,
            SUM(CleanAmount ( order_total )) sum_total
        "))
        ->whereNotIn('status', array(3))
        ->whereBetween('fecha_recibo', [$from, $to]);

       

        if($Stat != '') {
            $query->whereIn('status', array($Stat));
        }

        if($Ruta != '') {
            $query->where('ruta', $Ruta);
        }       
        

        $obj = $query->groupBy('ruta')->get()->toArray();


        
        foreach ($rutas as $ruta => $key){
            
            $found_key = array_search($key['VENDEDOR'], array_column($obj, 'ruta'));

            $isGrupoB = (in_array($key['VENDEDOR'], $GrupoB)) ? 'B' : 'A' ;



            $SUM_INGRESS = ($found_key === false) ? 0 : $obj[$found_key]->sum_ingress ;
            $SUM_PROCESS = ($found_key === false) ? 0 : $obj[$found_key]->sum_process ;
            $SUM_TOTAL = ($found_key === false) ? 0 : $obj[$found_key]->sum_total ;
            
            $COUNT_INGRESS = ($found_key === false) ? 0 : $obj[$found_key]->count_ingress ;
            $COUNT_PROCESS = ($found_key === false) ? 0 : $obj[$found_key]->count_process ;
            $COUNT_ANULA = ($found_key === false) ? 0 : $obj[$found_key]->count_anulado ;
            $COUNT_TOTAL = ($found_key === false) ? 0 : $obj[$found_key]->count_total ;

           

            
            $data[$i]["DETALLE"]            = '<a id="exp_more" class="exp_more" href="#!"><i class="material-icons expan_more">expand_more</i></a>';
            $data[$i]['VENDEDOR']           = $key['VENDEDOR'];
            $data[$i]['NOMBRE']             = $key['NOMBRE'];
            $data[$i]['GRUPO']             = $isGrupoB;


            
            $data[$i]['SUM_INGRESS']        = $SUM_INGRESS;
            $data[$i]['SUM_PROCESS']        = $SUM_PROCESS;
            $data[$i]['MONTO']              = $SUM_TOTAL;

            $data[$i]['COUNT_INGRESS']      = $COUNT_INGRESS;
            $data[$i]['COUNT_PROCESS']      = $COUNT_PROCESS;
            $data[$i]['COUNT_ANULA']        = $COUNT_ANULA;

            $data[$i]['COUNT_TOTAL']        = $COUNT_TOTAL;

            $i++;

            

            

        }


        
        return response()->json($data);

    }

    public static function getLiquidaciones(Request $request) {
        

        $from   = $request->input('f1').' 00:00:00';
        $to     = $request->input('f2').' 23:59:59';
        
        $Ruta   = $request->input('RU');

        $i=0;

        if (!$from) {
            return response()->json(false);
        }

        $data = array();

        $query = DB::table('view_liquidaciones')->whereBetween('created_at', [$from, $to]);

        if($Ruta != '') {
            $query->where('Ruta', $Ruta);
        }


        $obj = $query->get();

    
        foreach ($obj as $qR => $key) {
            $arrDetalles = array();

            $IdLiquidacion = vinetaliq_controller::addZeros($key->Id);

            $Fondo      = $key->Fondo_inicial;
            $Reembolso  = $key->Reembolso;
            $Saldo      = $Fondo - $Reembolso;


            $data[$i]["DETALLE"]        = '<a id="expa_recibos" class="expa_recibos" href="#!"><i class="material-icons expa_recibos ">expand_more</i></a>';
            $data[$i]['ID']             = $key->Id;
            $data[$i]['VENDEDOR']       = $key->Ruta;           
            $data[$i]['RUTA_NAME']      = $key->Ruta_name;           
            $data[$i]['FECHA']          = date('d/m/Y', strtotime($key->created_at));        
            $data[$i]['TOTAL']          = "";
            $data[$i]['FONDO']          = $Fondo;  
            $data[$i]['REEMBOLSO']      = $Reembolso;  
            $data[$i]['SALDO']          = $Saldo;  
            $data[$i]['RECIBO']         = $IdLiquidacion;

            $data[$i]['COMMENT']        = $key->Nota;
            $data[$i]['COMMENT_ANUL']   = "";
            

            


            $OrdenList  = $key->Lineas.",";
            $Lineas     = explode("],", $OrdenList);
            $cLineas    = count($Lineas) - 1;


            for ($l=0; $l < $cLineas ; $l++){
                
                $Lineas_detalles     = explode(";", $Lineas[$l]);

                $arrDetalles[$l]['FECHA']           = str_replace('[', '', $Lineas_detalles[0]);
                $arrDetalles[$l]['RECIBO']          = $Lineas_detalles[1];
                $arrDetalles[$l]['CLIENTE_NAME']    = $Lineas_detalles[2];
                $arrDetalles[$l]['CLIENTE_COD']     = $Lineas_detalles[3];
                $arrDetalles[$l]['CONCEPTO']        = $Lineas_detalles[4];
                $arrDetalles[$l]['TOTAL']           = $Lineas_detalles[5];

                
            }

            $data[$i]['DETALLES']       = $arrDetalles;

            $data[$i]["BOTONES"]        = ' <button type="button" class="btn btn-success float-center"   onClick="rePrint('.$key->Id.')">
                                                <i class="material-icons text-white mt-1"  style="font-size: 20px">local_printshop</i>
                                            </button>
                                            <button type="button" class="btn btn-danger float-center"   onClick="Delete('.$key->Id.')">
                                                <i class="material-icons text-white mt-1"  style="font-size: 20px">close</i>
                                            </button>';


            $i++;
        }
        
        return response()->json($data);

    }


    public static function UpdateStatus($ID,$Status,$Message){
		
        $request = Request();

        $vUpdate = array(
            'status'        =>  $Status,
            'comment_anul'  =>  $Message
        );
        
        recibos_model::where('id', $ID)->update($vUpdate);
        
        $Row = recibos_model::where('id',$ID)->first();

        if ($Status==1) {
            recibos_controller::SendNotifications("Solicitud Aceptada","Recibo Nº ".$Row->recibo." ",$Row->player_id);
        } else if($Status==2) {
            recibos_controller::SendNotifications("Solicitud Anulada","Recibo Nº ".$Row->recibo." ",$Row->player_id);
        }
        
        

	}

    public static function SendNotifications($Titulo,$Contenido,$userId){
        
        $OneSignal = app_onesignal::all();

        foreach($OneSignal as $OS){
            $onesignal_app_id       = $OS->onesignal_app_id;
            $onesignal_rest_api_key = $OS->onesignal_rest_api_key;
        }

        define("ONESIGNAL_APP_ID", $onesignal_app_id);
        define("ONESIGNAL_REST_KEY", $onesignal_rest_api_key);

        $content = array(
            "en" => $Contenido
        );
    
        $data_noti = array(
            'app_id' => ONESIGNAL_APP_ID,
            'include_player_ids' => array($userId),
            'data' => array("foo" => "bar", "cat_id"=> "1010101010"),
            'headings'=> array("en" => $Titulo),
            'contents' => $content
        );

        $data_noti = json_encode($data_noti);


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic '.ONESIGNAL_REST_KEY));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_noti);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);
    }
    public function cancelarliq(Request $request){
        if($request->isMethod('post')) {
            $id = $request->input('id');
            $me = $request->input('me');
            vinetaliq_controller::UpdateStatus($id,2,$me);
        }
    }
    public function Deleteliq(Request $request){

        if($request->isMethod('post')) {

            $id = $request->input('id');

            $vUpdate = array(
                'Anulado'        =>  "S"
            );
            
            tbl_liquidacion_fondo_vineta::where('Id', $id)->update($vUpdate);
        }
    }

    public function push_recibo(Request $request){
        if($request->isMethod('post')) {
            $id = $request->input('id');            
            recibos_controller::UpdateStatus($id,1,"");
        }
        
    }
    public function push_verificado(Request $request){
        if($request->isMethod('post')) {
            $id = $request->input('id');            
            recibos_controller::UpdateStatus($id,2,"");
        }
        
    }

    public function AnularVineta(Request $request){
        $sql_server = new \sql_server();
        $Sql = "";
        if($request->isMethod('post')) {

            $lVENDEDOR      = "F00";
            $lRECIBO        = "DEVO";
            $lCLIENTE       =  $request->input('Cliente');
            $lFECHA         =  date('Y-m-d H:i:s');    
            $lFACTURA       = $request->input('Factura');
            $lVOUCHER       = $request->input('Vineta');
            $lCANTIDAD      = $request->input('Cantida');
            $lVALOR_UNIT    = $request->input('ValorUnd');
            $lLINEA         = $request->input('Linea'); 
            $lCOMMENT       = $request->input('Coment'); 

            $Sql = "INSERT INTO [DESARROLLO].[dbo].[tbl_vineta_liquidadas] ([FACTURA], [VOUCHER], [LINEA], [CANTIDAD], [CLIENTE], [RUTA], [FECHA], [COD_RECIBO], [VALOR_UND], [COMMENT]) 
                    VALUES ('".$lFACTURA."', '".$lVOUCHER."', '$lLINEA', '$lCANTIDAD', '".$lCLIENTE."', '".$lVENDEDOR."', '$lFECHA', '".$lRECIBO."', '$lVALOR_UNIT', '$lCOMMENT') ";
            $sql_server->fetchArray($Sql, SQLSRV_FETCH_ASSOC);
        }
        
    }

    public function getVinnetasResumen(Request $request) {

        $f1 = $request->input('f1');
        $f2 = $request->input('f2');

        

        if (!$f1) {
            return response()->json(false);
        }

        $obj = vinneta_model::getVinnetasResumen($f1, $f2);

        return response()->json($obj);

    }

    public function getDetalleOrdenCompra(Request $request){
        if($request->isMethod('post')) {
            $obj = vinneta_model::getDetalleOrdenCompra($request->input('ordCompra'));
            return response()->json($obj);
        }
    }

    public static function addZeros($code){
        $res='';
        switch (strlen($code)) {           
            case 3:
                $res = '0'.$code;
                break;
            case 2:
                $res = '00'.$code;
                break;
            case 1:
                $res = '000'.$code;
                break;
            
            default:
                $res = $code;
                break;
        }

        return $res;
        
    }

    public static function getStatus($code){
        $res='';
        switch ($code) {           
            case 0:
                $res = 'Pendiente';
                break;
            case 1:
                $res = 'Ingresado';
                break;
            case 2:
                $res = 'Verificado';
                break;
            
            default:
                $res = $code;
                break;
        }

        return $res;
        
    }

    public static function isAdjunto($id){
        $query = DB::table('tbl_order_recibo_adjuntos')->where('id_recibo',$id)->count();

        $isAttanche = ($query > 0) ? 'SI' : 'NO' ;
        return $isAttanche;
        
    }


}
