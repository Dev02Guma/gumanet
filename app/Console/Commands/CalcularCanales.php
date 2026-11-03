<?php

namespace App\Console\Commands;

use App\ContribucionPorCanales;
use Illuminate\Console\Command;

class CalcularCanales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cmd:CalcularCanales
                            {fechaIni? : Inicio del período (YYYY-MM-DD). Por defecto: día 1 del mes, 12 meses atrás}
                            {fechaFin? : Fin del período (YYYY-MM-DD). Por defecto: último día del mes anterior}
                            {--probar : Solo muestra el período, no calcula}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalcula la Contribución por canales (/canales), igual que el botón Calcular de la pantalla';

    /**
     * Execute the console command.
     * Pensado para el cron del día 1 de cada mes, antes de cmd:EnviarReorderPoint:
     * por defecto calcula los 12 meses completos anteriores (ej. el 1-nov-2026: 01/11/2025 al 31/10/2026).
     *
     * @return int
     */
    public function handle()
    {
        $fechaIni = $this->argument('fechaIni') ?: date('Y-m-01', strtotime('first day of -12 months'));
        $fechaFin = $this->argument('fechaFin') ?: date('Y-m-d', strtotime('last day of previous month'));

        foreach ([$fechaIni, $fechaFin] as $f) {
            $d = \DateTime::createFromFormat('Y-m-d', $f);
            if (!$d || $d->format('Y-m-d') !== $f) {
                $this->error('Fecha inválida: ' . $f . ' (formato YYYY-MM-DD)');
                return 1;
            }
        }

        // Fin con hora: las ventas del último día tienen hora y no deben quedar fuera del período
        $hasta = $fechaFin . ' 23:59:59';
        $this->info(date('Y-m-d H:i:s') . " · Contribución por canales del {$fechaIni} al {$hasta}");

        if ($this->option('probar')) {
            $this->info('Modo prueba: no se calculó nada.');
            return 0;
        }

        ContribucionPorCanales::calcularCanales($fechaIni, $hasta);

        $this->info(date('Y-m-d H:i:s') . ' · Cálculo de canales terminado');
        return 0;
    }
}
