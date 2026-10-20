<?php

namespace App\Http\Controllers;

use App\Revalorizacion;
use Illuminate\Http\Request;

class RevalorizacionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','roles']);
    }

    public function index()
    {
        return view('pages.Revalorizacion.home');
    }

    public function getBodegas()
    {
        $Bodegas = Revalorizacion::getBodegas();

        return response()->json([
            'BODEGAS' => $Bodegas
        ]);
    }

    public function getRevalorizacion(Request $request)
    {
        set_time_limit(0);

        $Revalorizacion = Revalorizacion::Filtrar($request);
        return response()->json($Revalorizacion);
    }

    /**
     * Excel de la revalorización: vuelve a calcular el corte con los mismos filtros de la pantalla
     * (?fecha=YYYY-MM-DD&bodegas=002,005&agrupar=ARTICULO|LOTE) para que el archivo siempre corresponda a esa fecha.
     */
    public function ExportRevalorizacion(Request $request)
    {
        set_time_limit(0);

        $fecha   = (string) $request->input('fecha');
        $bodegas = array_filter(explode(',', (string) $request->input('bodegas')), 'strlen');
        $porArticulo = Revalorizacion::esPorArticulo($request->input('agrupar'));
        $data    = Revalorizacion::Generar($bodegas, $fecha);
        if ($porArticulo) {
            $data = Revalorizacion::AgruparPorArticulo($data);
        }

        $nombres = [];
        foreach (Revalorizacion::getBodegas() as $b) {
            if (in_array($b['BODEGA'], $bodegas)) {
                $nombres[] = $b['BODEGA'] . ' - ' . $b['NOMBRE'];
            }
        }

        $objPHPExcel = Revalorizacion::Excel($data, $fecha, $nombres, public_path('img/unimark.png'), $porArticulo);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Revalorizacion_Inventario_' . ($porArticulo ? 'Articulos_' : 'Lotes_') . $fecha . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}
