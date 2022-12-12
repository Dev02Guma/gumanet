<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Projects extends Model
{
	protected $table = "fc_db.projects";

	 public function dtCompanie(){
        return $this->belongsTo('App\Companies','company_id','id');
    }

	public function Tasks(){
        return $this->hasMany('App\TasksProjects','project_id','id');
    }

	public function ttTasks() {
        return $this->Tasks()->selectRaw('status,count(status) hit')->groupBy('project_id','status');
    }
    

	public static function getProjects()
    {
		$response = array();
		
		$i = 0;

		$projects = Projects::all();

		foreach($projects as $p){


			$h = $p->ttTasks;

			//dd($h[0]['hit']);
			$str = '0 / 0';

			if(count($h) > 0){
				$open = (!empty($h[0]['hit']))? $h[0]['hit']: 0;
				$done = (!empty($h[1]['hit']))? $h[1]['hit']: 0;
				$str = $done . ' / ' . ($open + $done);
			}

			$response[$i]['id']     		=  $p->id;
			$response[$i]["DETALLE"]        = '<a id="exp_more" class="exp_more" href="#!"><i class="material-icons expan_more">expand_more</i></a>';
			$response[$i]['reference']      =  $p->reference;
			$response[$i]['name']    		=  $p->name;
			$response[$i]['company_id']    	=  $p->dtCompanie->name;
			$response[$i]['end']     		=  $p->end;
			$response[$i]['progress']     	=  $p->progress.' %';
			$response[$i]['tasks']     		=  $str;
			$i++;
		}

		return $response;
	}
	public static function getTasks(Request $request)
    {
		if ($request->ajax()) {
            try {
                $id  = $request->input('id');
				$response =array();
				$i=0;
				$Priority = array('Bajo','Medio','Alto');

                $objProjects = Projects::where('id',$id)->get();

				foreach($objProjects[0]->Tasks as $p){

					$check = ($p->status =='open') ? '' : '<img src="./images/success.png" class="img01" />';



					$iRow = '<div class="row" >
									<div class="col-12">
										<p class="float-left font-weight-bold mr-4">'.$p->name.'</p>
										<p class="float-right font-weight-bold mr-4" style="display:none"><img src="./images/info.png" class="img01" /> </p>
										<p class="float-right font-weight-bold mr-4"> '.$check.' </p>
									</div>
								</div>
							</div>';


					

					$response[$i]['name']     		= $iRow;
					$response[$i]['status']      	=  ($p->status =='open') ? 'Pendiente' : 'Hecho';
					$response[$i]['priority']    	=  $Priority[$p->priority - 1];
					$response[$i]['start_date']     =  (!empty($p->start_date))? $p->start_date : 'N/D';
					$response[$i]['dute_date']     	=  (!empty($p->due_date))? $p->due_date : 'N/D';
					$i++;
				}
				
                return response()->json($response);

            } catch (Exception $e) {
                $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
                return response()->json($mensaje);
            }
        }

	}
}
