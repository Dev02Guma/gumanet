<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models;
use App\Company;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use App\inteligenciaMercado_model;
use DB;

class inteligenciaMercado_controller extends Controller
{
	public function __construct() {
		$this->middleware(['auth','roles']);//pagina se carga unicamente cuando se este logeado
	}

	public function index(Request $request) {
		$this->agregarDatosASession();
		$company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;
		
		$comentarios = inteligenciaMercado_model::where('empresa', $company_user)->orderBy('Fecha', 'desc')->paginate(5);
		
		$data = [
			'page' 				=> 'Inteligencia de Mercado',
			'name' 				=> 'GUMA@NET',
			'hideTransaccion' 	=> '',
			'comentarios'		=> $comentarios
		];	


		inteligenciaMercado_controller::Update();

		return view('pages.inteligenciaMercado', $data);		
	}

    public function agregarDatosASession() {
        $request = Request();
        $ApplicationVersion = new \git_version();
        $company = Company::where('id',$request->session()->get('company_id'))->first();// obtener nombre de empresa mediante el id de empresa
        $request->session()->put('ApplicationVersion', $ApplicationVersion::get());
        $request->session()->put('companyName', $company->nombre);// agregar nombre de compañia a session[], para obtenert el nombre al cargar otras pagina 
    }

    public function searchComentarios(Request $request) {
		if($request->isMethod('post')) {
			$company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;

			$search 	= $request->input('search');
			$search 	=  '%' . $search . '%';
			
			$date 		= $request->input('date');
			$order 		= ( $date=='desc' )?'desc':'asc';
			
			$dates 		= $request->input('fechas');

			$from = ( $dates==null )?date('Y-m-d h:i:s', strtotime('2020-01-01 00:00:00')):date('Y-m-d 00:00:00', strtotime($dates['fecha1']));
			$to = ( $dates==null )?date('Y-m-d 23:59:59'):date('Y-m-d 23:59:59', strtotime($dates['fecha2']));
			
			$comentarios = inteligenciaMercado_model::where(function($q) use ($search) {
				$q->where('Nombre', 'LIKE', $search)->orWhere('Titulo', 'LIKE', $search)->orWhere('Contenido', 'LIKE', $search)->orWhere('Autor', 'LIKE', $search);
			})->where('empresa', $company_user)->whereBetween('Fecha', [$from, $to])->orderBy('Fecha', $order)->paginate(5);

			return view('pages.comentarios', compact('comentarios'))->render();
		}
    }

	public function countim(Request $request){
		$company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;	

		return inteligenciaMercado_model::where('Read', '=', 0)->where('empresa', $company_user)->count();
		

	}

	public static function Update(){
		$request = Request();
		$company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;	
		inteligenciaMercado_model::where('Read',"=", 0)->where('empresa', $company_user)->update(['Read' => 1]);

	}


    public function descargarComentarios( Request $request ) {
    	setlocale(LC_TIME, "spanish");
    	$base = config('global.url_server');
    	$company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;
		
		if($request->isMethod('post')) {
			$search 	= $request->input('valueFiltro_');
			$search 	=  '%' . $search . '%';
			
			$date 		= $request->input('valueDate_');
			$order 		= ( $date=='desc' )?'desc':'asc';
			
			$fecha1		= $request->input('valueFecha1');
			$fecha2		= $request->input('valueFecha2');

			$from = ( $fecha1=='ND' )?date('Y-m-d h:i:s', strtotime('2020-01-01 00:00:00')):date('Y-m-d h:i:s', strtotime($fecha1));
			$to = ( $fecha1=='ND' )?date('Y-m-d 23:59:59'):date('Y-m-d 23:59:59', strtotime($fecha2));
			
			$comentarios = inteligenciaMercado_model::where(function($q) use ($search) {
				$q->where('Nombre', 'LIKE', $search)->orWhere('Titulo', 'LIKE', $search)->orWhere('Contenido', 'LIKE', $search)->orWhere('Autor', 'LIKE', $search);
			})->where('empresa', $company_user)->whereBetween('Fecha', [$from, $to])->orderBy('Fecha', $order)->paginate(5);
		}

		$objPHPExcel = new PHPExcel();
        $tituloReporte = "Inteligencia de Mercado";
        $titulosColumnas = array('Titulo', 'Nombre', 'Contenido', 'Ruta', 'Fecha', 'Archivo Adjunto');


        if ($fecha1=='ND' && $fecha2=='ND') {
        	$subTituloRpt = "Mostrando todos los comentarios hasta la fecha";
        }else {
        	$subTituloRpt = "Generado desde ".date('d/m/y', strtotime($from)).' hasta '.date('d/m/y', strtotime($to));
        }

		$negrita = array(
			'font' => array(
				'name'      => 'Calibri',
				'bold'      => true
			)
		);

		$borders = array(
			'borders' => array(
				'top' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
			'left' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
			),
			'right' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
			),
			'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				)
			)
		);

		$estiloTituloReporte = array(
			'font' => array(
				'name'      => 'Calibri',
				'bold'      => true,
				'italic'    => false,
				'strike'    => false,
				'size' =>15,
				'color'     => array(
								'rgb' => '151515'
				)
			),
			'alignment' =>  array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			'rotation'   => 0,
			'wrap'       => TRUE,
			)
		);

		$estilosubTituloReporte = array(
			'font' => array(
				'name'      => 'Calibri',
				'bold'      => false,
				'italic'    => false,
				'strike'    => false,
				'size' =>12,
				'color'     => array(
								'rgb' => '151515'
				)
			),
			'alignment' =>  array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'rotation'   => 0,
				'wrap'       => TRUE,
			)
		);

		$styleArray = array(
			'font'  => array(
			'bold'  => true,
			'color' => array('rgb' => '0000FF'),
				'name'  => 'Calibri'
		));

		$objPHPExcel->setActiveSheetIndex(0)
		->mergeCells('A1:D1');
		$objPHPExcel->setActiveSheetIndex(0)
		->mergeCells('A2:D2');
		$objPHPExcel->setActiveSheetIndex(0)
		->mergeCells('A3:D3');

		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', $tituloReporte)
		->setCellValue('A2', $subTituloRpt);

		$i=4;

        foreach ($comentarios as $key) {
			$y=$i;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i,  $titulosColumnas[0])
			->setCellValue('B'.$i,  $key['Titulo'])
			->mergeCells('B'.$i.':D'.$i)
			->getColumnDimension('B')->setWidth(30);
			$i++;

			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i,  $titulosColumnas[1])
			->mergeCells('B'.$i.':D'.$i)
			->setCellValue('B'.$i,  $key['Nombre'])
			->getColumnDimension('B')->setWidth(100);
			$i++;

			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i,  $titulosColumnas[2])
			->mergeCells('B'.$i.':D'.$i)
			->setCellValue('B'.$i,  $key['Contenido']);
                
			$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()
			->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP )
			->setWrapText(true)
			->getActiveSheet()
			->getRowDimension($i)->setRowHeight(45);
			$i++;

			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i,  $titulosColumnas[3])
			->mergeCells('B'.$i.':D'.$i)
			->setCellValue('B'.$i,  $key['Autor']);
			$i++;

			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i,  $titulosColumnas[4])
			->mergeCells('B'.$i.':D'.$i)
			->setCellValue('B'.$i,  strftime('%a %d de %b %G', strtotime($key['Fecha'])).'. '.date('h:i a', strtotime($key['Fecha'])) );
			$i++;
			
			if ( $key['Imagen']!='' || $key['Imagen']!=NULL ) {
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$i,  $titulosColumnas[5])
				->mergeCells('B'.$i.':D'.$i)
				->setCellValue('B'.$i,  '1 Imagen adjunta')
				->getCell('B'.$i)->getHyperlink()->setUrl($base.$key['Imagen']);

				$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setUnderline(true);
				$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray($styleArray);
			}else {
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$i,  $titulosColumnas[3])
				->mergeCells('B'.$i.':D'.$i)
				->setCellValue('B'.$i,  'No hay archivos adjuntos');
			}
			$i++;

            $objPHPExcel->setActiveSheetIndex(0)
                ->getStyle('A'.$y.':A'.($i-1))->applyFromArray($borders);

            $objPHPExcel->setActiveSheetIndex(0)
                ->getStyle('B'.$y.':D'.($i-1))->applyFromArray($borders);
			$i++;
        }

        $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->applyFromArray($estiloTituloReporte);
		$objPHPExcel->getActiveSheet()->getStyle('A2:E2')->applyFromArray($estilosubTituloReporte);
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.($i+1))
		->applyFromArray($negrita);
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.($i+1))->getAlignment()
		->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP )
		->setWrapText(true);
		
		$objPHPExcel->getActiveSheet()->setTitle('REPORTE COMENTARIOS');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Inteligencia de mercado '.date('d/m/Y').'.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
    }
}


