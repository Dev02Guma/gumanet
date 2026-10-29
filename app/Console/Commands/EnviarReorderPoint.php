<?php

namespace App\Console\Commands;

use App\ReOrderPoint2;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EnviarReorderPoint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cmd:EnviarReorderPoint
                            {--verificar-corte : No envía si /canales no está calculado hasta el último día del mes anterior}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera el Excel del Reorder Point (política v2.1) y lo envía por correo a los destinatarios';

    /**
     * Execute the console command.
     * Pensado para el cron del día 1 de cada mes; usa la misma lógica que la ruta SendReOrderPoint.
     *
     * @return int
     */
    public function handle()
    {
        // PHPExcel (abandonado) genera avisos de deprecado en PHP 7.4 que Laravel convierte en excepción
        error_reporting(E_ALL & ~E_DEPRECATED);

        // En el cron va después de cmd:CalcularCanales: si ese cálculo falló o no terminó, no se envía con el corte viejo
        if ($this->option('verificar-corte')) {
            $corte    = DB::connection('sqlsrv')->select("SELECT MAX(fecha) AS FIN FROM PRODUCCION.dbo.tbl_contribucion_canales")[0]->FIN;
            $esperado = date('Y-m-d', strtotime('last day of previous month'));
            if (substr((string) $corte, 0, 10) < $esperado) {
                $this->error(date('Y-m-d H:i:s') . ' · No se envió: /canales está calculado hasta '
                    . substr((string) $corte, 0, 10) . ' y se esperaba ' . $esperado . ' (revisar cmd:CalcularCanales)');
                return 1;
            }
        }

        $this->info(date('Y-m-d H:i:s') . ' · Enviando Reorder Point...');

        $res = ReOrderPoint2::SendReOrderPoint([]);

        if (empty($res['status'])) {
            $this->error($res['message'] ?? 'No se pudo enviar el Reorder Point');
            return 1;
        }

        $this->info($res['message'] . ' · ' . ($res['productos'] ?? 0) . ' productos');
        return 0;
    }
}
