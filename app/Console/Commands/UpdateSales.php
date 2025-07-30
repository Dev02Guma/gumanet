<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\InnovaController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Logs_calcs;


class UpdateSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ReorderPoint:UpdateSales';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        define( 'WP_MAX_MEMORY_LIMIT' , '512M' );

        $currentDate = date('Y-m-d');
        $startOfMonth = date('Y-m-01', strtotime($currentDate));

        $FechaIni   = date('Y-m-d 00:00:00.000', strtotime('-11 months', strtotime($startOfMonth)));
        $FechaEnd   = date('Y-m-d 00:00:00.000', strtotime($currentDate . ' -1 days'));
        $DiaActual  = (int) date('d', strtotime($FechaEnd)); 

        // Insertar en el modelo Logs_calcs
        Logs_calcs::create([
            'Modulo'        =>  'ReOrderPoint',
            'ini'           => $FechaIni,
            'end'           => $FechaEnd,
            'Observacion'   => 'Actualizacion de Facturas al : ' . $DiaActual
        ]);

        // Este procedimiento , actualiza la informacion de facturaas      
        DB::connection('sqlsrv')->select("EXEC PRODUCCION.dbo.pr_gnet_reorder_UpdateSales");
        
        

        return response()->json(['message' => 'Success'], 200);
    }
}
