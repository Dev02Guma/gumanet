<?php

namespace App\Http\Controllers;

use App\ReOrderPoint2;
use Illuminate\Http\Request;

class ReOrderPoint2Controller extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','roles']);
    }

    public function index()
    {
        return view('pages.ReOrderPoint2.home');
    }

    public function getData(Request $request)
    {
        return response()->json(ReOrderPoint2::getData());
    }

    /**
     * Excel con la estructura y estilo de la hoja Decision_x_Producto.
     * Recibe los artículos que quedaron en pantalla con los filtros (articulos[]); sin lista exporta todo.
     */
    public function ExportReOrderPoint2(Request $request)
    {
        set_time_limit(0);

        $res  = ReOrderPoint2::getData();
        $data = $res['DATA'] ?? [];

        $articulos = (array) $request->input('articulos', []);
        if (count($articulos)) {
            $data = array_values(array_filter($data, function ($r) use ($articulos) {
                return in_array((string) $r['ARTICULO'], $articulos, true);
            }));
        }

        $meta  = $res['META'] ?? [];
        $fecha = $meta['FECHA_CORTE'] ?? date('Y-m-d');

        $objPHPExcel = ReOrderPoint2::Excel($meta, $res['HEADERS'] ?? [], $data);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Decision_x_Producto_' . $fecha . '.xlsx"');
        header('Cache-Control: max-age=0');

        // PHPExcel (abandonado) escribe el autofiltro con count(bool), que en PHP 7.2+ lanza un warning:
        // se silencian warnings y deprecados solo mientras se escribe el archivo
        $nivel = error_reporting(E_ALL & ~E_WARNING & ~E_DEPRECATED);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        error_reporting($nivel);
        exit;
    }
}
