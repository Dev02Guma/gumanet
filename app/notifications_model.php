<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\inteligenciaMercado_model;
use App\Company;

class notifications_model extends Model
{
    //
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = ['usuario_id', 'nombre','title', 'message', 'leido'];

    public static function getAllnotificaciones()
    {
        $data = array();
        $i = 0;
        $notificaciones = notifications_model::select('notifications.*')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($notificaciones as $dataN) {
            $data[$i]['id']            = $dataN['id'];
            $data[$i]['user_id']       = $dataN['usuario_id'];
            $data[$i]['nombre']        = $dataN['nombre'];
            $data[$i]['title']         = $dataN['title'];
            $data[$i]['message']       = $dataN['message'];
            $data[$i]['leido']         = $dataN['leido'];
            $data[$i]['created_at']    = carbon::parse($dataN['created_at'])->diffForHumans();
            $data[$i]['updated_at']    = $dataN['updated_at'];
            $i++;
        }


        return $data;
    }

    public static function updateState()
    {
        $notificaciones = notifications_model::where('leido', 0)
            ->update([
                'leido' => 1,
            ]);

        return $notificaciones;
    }

    public static function exist_notify(Request $request)
    {
        $client = new Client;
        $data = array();
        $count_IM = 0;
        $count_expo = 0;
        //Get Notificaciones  de expor taciones;
        $notificacion = new notifications_model();
        $data_Exp = $notificacion->getAllnotificaciones();

        $company_user = Company::where('id', $request->session()->get('company_id'))->first()->id;
        $count_IM = inteligenciaMercado_model::where('Read', '=', 0)->where('empresa', $company_user)->count();

        foreach ($data_Exp as $resp) {
            if ($resp['leido'] == 0) {
                $count_expo++;
            }
        }

        $total = $count_expo +  $count_IM;
        return $total;
    }

    public static function exist_registry(Request $request)
    {

        $client = new Client;
        $count_IM = 0;
        $count_expo = 0;
        //Get Notificaciones  de expor taciones;
        $notificacion = new notifications_model();
        $data_Exp = $notificacion->getAllnotificaciones();
        $company_user = Company::where('id', $request->session()->get('company_id'))->first()->id;
        $count_IM = inteligenciaMercado_model::where('empresa', $company_user)->count();

        $count_expo = (empty($data_Exp)) ?  0 : 1;
        $total = $count_expo +  $count_IM;
        return $total;
    }
}
