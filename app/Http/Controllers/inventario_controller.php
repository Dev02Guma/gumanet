<?php
namespace App\Http\Controllers;

use App\inventario_model;
use Illuminate\Http\Request;
use App\Models;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use App\Company;

class inventario_controller extends Controller
{
	public function __construct() {
		$this->middleware(['auth','roles']);//pagina se carga unicamente cuando se este logeado
  	}

	public function index() {
		$this->agregarDatosASession();

		$data = array(
			'page' 				=> 'Inventario',
			'name' 				=> 'GUMA@NET',
			'hideTransaccion' 	=> ''
		);
		return view('pages.inventario', $data);
	}

    public function agregarDatosASession(){
        $request = Request();
        $ApplicationVersion = new \git_version();
        $company = Company::where('id',$request->session()->get('company_id'))->first();// obtener nombre de empresa mediante el id de empresa
        $request->session()->put('ApplicationVersion', $ApplicationVersion::get());
        $request->session()->put('companyName', $company->nombre);// agregar nombre de compañia a session[], para obtenert el nombre al cargar otras pagina 
    }

    public function inventarioTotalizado() {
		$this->agregarDatosASession();

		$data = array(
			'page' 				=> 'Inventario',
			'name' 				=> 'GUMA@NET',
			'hideTransaccion' 	=> ''
		);
		return view('pages.invTotalizado', $data);
    }

    public function inventarioCompleto() {
    	$this->agregarDatosASession();

    	$data = array(
    		'page'		=> 'Inventario Completo',
    		'name'		=> 'GUMA@NET',
    		'hideTransaccion' => ''
    	);

    	return view('pages.inventarioCompleto', $data);
    }

    public function inventarioCompletoTable() {
		$obj = inventario_model::getInventarioCompleto();
		return response()->json($obj);
    }

	public function invenVencidos() {
		$obj = inventario_model::invenVencidos();
		return response()->json($obj);
    }

	public function getArticulos() {
		$obj = inventario_model::getArticulos();
		return response()->json($obj);
	}

	public function descargarInventarioCompleto() {

		$obj = inventario_model::getInventarioCompleto();

		$objPHPExcel = new PHPExcel();
        $tituloReporte = "Inventario";
		
        $titulosColumnas = array('BODEGA','ARTICULO','DESCRIPCION','ACTIVO','LABORATORIO','UNIT.MED','LOTE','CANT_DISPONIBLE','FCH.VENCIMIENTO','CODIGO_BARRAS_VENT');
		$objPHPExcel->setActiveSheetIndex(0)
                        ->mergeCells('A1:J1');
		$objPHPExcel->setActiveSheetIndex(0)
                        ->mergeCells('A2:J2');

        $estiloTituloReporte = array(
            'font' => array(
            'name'      => 'Calibri',
            'bold'      => true,
            'italic'    => false,
            'strike'    => false,
            'size'      => 14,
            'color'     => array(
                            'rgb' => '212121')
            ),
            'alignment' =>  array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                            'rotation'   => 0,
                            'wrap'       => TRUE,
                            )
        );

        $estiloTituloColumnas = array(
            'font' => array(
                        'name'  => 'Calibri',
                        'bold'  => true
            ),
            'alignment' =>  array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                'wrap'          => TRUE
                            ),
            'borders' => array(
                            'top' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                        ),
            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                            )
            )
        );

        $estiloInformacion = new PHPExcel_Style();
        $estiloInformacion->applyFromArray(
            array(
                'borders' => array(
                'top' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                        ),
                'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                                ),
                )
            )
        );

        $right = array(
            'alignment' =>  array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => TRUE
            )
        );

        $left = array(
            'alignment' =>  array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => TRUE
            )
        );

		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1',    $tituloReporte)
		->setCellValue('A3',    $titulosColumnas[0])
		->setCellValue('B3',    $titulosColumnas[1])
		->setCellValue('C3',    $titulosColumnas[2])
		->setCellValue('D3',    $titulosColumnas[3])
		->setCellValue('E3',    $titulosColumnas[4])
		->setCellValue('F3',    $titulosColumnas[5])
		->setCellValue('G3',    $titulosColumnas[6])
		->setCellValue('H3',    $titulosColumnas[7])
		->setCellValue('I3',    $titulosColumnas[8])
		->setCellValue('J3',    $titulosColumnas[9]);

		$i=4;
		foreach ($obj as $key) {
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i,  $key['BODEGA'])
			->setCellValue('B'.$i,  $key['ARTICULO'])
			->setCellValue('C'.$i,  $key['DESCRIPCION'])
			->setCellValue('D'.$i,  $key['ACTIVO'])
			->setCellValue('E'.$i,  $key['LABORATORIO'])
			->setCellValue('F'.$i,  $key['UNIDAD_MEDIDA'])
			->setCellValue('G'.$i,  $key['LOTE'])
			->setCellValue('H'.$i,  $key['CANT_DISPONIBLE'])
			->setCellValue('I'.$i,  $key['FECHA_VENCIMIENTO'])
			->setCellValue('J'.$i,  $key['CODIGO_BARRAS_VENT']);
			$i++;
		}

		$objPHPExcel->getActiveSheet()->setTitle('Inventario');
		$objPHPExcel->getActiveSheet()->getStyle('A1:J1')->applyFromArray($estiloTituloReporte);
		$objPHPExcel->getActiveSheet()->getStyle('A3:J3')->applyFromArray($estiloTituloColumnas);      
		$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:J".($i-1));

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(80);
		
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Inventario hasta '.date('d/m/Y').'.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');


	}

	public function descargarInventarioTotalizado() {

		$obj = inventario_model::getInventarioTotalizado();

		$objPHPExcel = new PHPExcel();
        $tituloReporte = "Inventario Totalizado";
		
        $titulosColumnas = array('Articulo', 'Descripcion', 'Laboratorio', 'Unidad', 'Bodega UMK', 'Bodega INN');
		$objPHPExcel->setActiveSheetIndex(0)
                        ->mergeCells('A1:F1');
		$objPHPExcel->setActiveSheetIndex(0)
                        ->mergeCells('A2:F2');

        $estiloTituloReporte = array(
            'font' => array(
            'name'      => 'Calibri',
            'bold'      => true,
            'italic'    => false,
            'strike'    => false,
            'size'      => 14,
            'color'     => array(
                            'rgb' => '212121')
            ),
            'alignment' =>  array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                            'rotation'   => 0,
                            'wrap'       => TRUE,
                            )
        );

        $estiloTituloColumnas = array(
            'font' => array(
                        'name'  => 'Calibri',
                        'bold'  => true
            ),
            'alignment' =>  array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                'wrap'          => TRUE
                            ),
            'borders' => array(
                            'top' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                        ),
            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                            )
            )
        );

        $estiloInformacion = new PHPExcel_Style();
        $estiloInformacion->applyFromArray(
            array(
                'borders' => array(
                'top' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                        ),
                'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                                ),
                )
            )
        );

        $right = array(
            'alignment' =>  array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => TRUE
            )
        );

        $left = array(
            'alignment' =>  array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => TRUE
            )
        );

		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1',    $tituloReporte)
		->setCellValue('A3',    $titulosColumnas[0])
		->setCellValue('B3',    $titulosColumnas[1])
		->setCellValue('C3',    $titulosColumnas[2])
		->setCellValue('D3',    $titulosColumnas[3])
		->setCellValue('E3',    $titulosColumnas[4])
		->setCellValue('F3',    $titulosColumnas[5]);

		$i=4;
		foreach ($obj as $key) {
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i,  $key['ARTICULO'])
			->setCellValue('B'.$i,  $key['DESCRIPCION'])
			->setCellValue('C'.$i,  $key['UNIDAD_MEDIDA'])
			->setCellValue('D'.$i,  $key['LABORATORIO'])
			->setCellValue('E'.$i,  $key['B_UMK'])
			->setCellValue('F'.$i,  $key['B_INV']);
			$i++;
		}

		$objPHPExcel->getActiveSheet()->setTitle('Inventario Totalizado');
		$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($estiloTituloReporte);
		$objPHPExcel->getActiveSheet()->getStyle('A3:F3')->applyFromArray($estiloTituloColumnas);      
		$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:F".($i-1));

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(100);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(18);

		$objPHPExcel->getActiveSheet()->getStyle('E3:F'.($i-1))->getNumberFormat()->setFormatCode('#,##0.00');

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Inventario totalizado hasta '.date('d/m/Y').'.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');


	}

	public function getInventarioTotalizado() {
		$obj = inventario_model::getInventarioTotalizado();
		return response()->json($obj);
	}

	public function liquidacionMeses($valor) {
		$obj = inventario_model::dataLiquidacionMeses($valor);
		return response()->json($obj);
	}

	public function descargarInventario($tipo, $valor) {
		$obj = inventario_model::descargarInventario($tipo, $valor);
	}

	public function getArticuloDetalle($articulo) {
		$obj = inventario_model::getArticuloDetalle($articulo);
		return response()->json($obj);
	}

	public function getBodegaInventario($articulo) {
		$obj = inventario_model::getBodegaInventario($articulo);
		return response()->json($obj);
	}

	public function getPreciosArticulos($articulo) {
		$obj = inventario_model::getPreciosArticulos($articulo);
		return response()->json($obj);
	}

	public function getMargenArticulos($articulo) {
		$obj = inventario_model::getMargenArticulos($articulo);
		return response()->json($obj);
	}

	public function getCostosArticulos($articulo) {
		$obj = inventario_model::getCostosArticulos($articulo);
		return response()->json($obj);
	}
	public function getOtrosArticulos($articulo) {
		$obj = inventario_model::getOtrosArticulos($articulo);
		return response()->json($obj);
	}

	public function objIndicadores($articulo) {
		$obj = inventario_model::objIndicadores($articulo);
		return response()->json($obj);
	}


	public function getArtBonificados($articulo) {
		$obj = inventario_model::getArtBonificados($articulo);
		return response()->json($obj);
	}

	public function transaccionesDetalle(Request $request) {
		if($request->isMethod('post')){
			$obj = inventario_model::transaccionesDetalle($request->input('f1'),$request->input('f2'),$request->input('art'),$request->input('tp'));
			return response()->json($obj);
		}
	}

	public function getLotesArticulo(Request $request) {
		if($request->isMethod('post')){
			$obj = inventario_model::getLotesArticulo($request->input('bodega'),$request->input('articulo'));
			return response()->json($obj);
		}
	}
}
