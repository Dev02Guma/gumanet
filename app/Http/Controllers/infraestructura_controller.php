<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models;
use App\Projects;
use App\Companies;
class infraestructura_controller extends Controller
{

    public function __construct() 
    {
		$this->middleware(['auth','roles']);
    }

    public function home() 
    {

		$data = array(
			'page' 				=> 'Inventario',
			'name' 				=> 'GUMA@NET',
			'hideTransaccion' 	=> ''
		);

		
    $Companies = Companies::where('inactive',0)->get();
    return view('pages.infraestructura', compact('data','Companies'));
    }
    
    public function getProyects() 
    {
		$obj = Projects::getProjects();
		return response()->json($obj);
    }

    public function getTasksProjects(Request $request) 
    {        
		$response = Projects::getTasks($request);
        return response()->json($response);
    }

}