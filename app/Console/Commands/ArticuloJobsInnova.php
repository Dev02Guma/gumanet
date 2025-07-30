<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\InnovaController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Logs_calcs;


class ArticuloJobsInnova extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inn:job_gmv_articulo';

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
        
        // Este procedimiento , actualiza la informacion de facturaas      
        DB::connection('sqlsrv')->select("EXEC PRODUCCION.dbo.ArticuloJob_gmv_inn");

        return response()->json(['message' => 'Success'], 200);
    }
}
