<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Clients;

class ClientsController extends Controller
{
	public function index(){

	    $json = array(

	    	"detalle"=>"no encontrado" 
	    );
	    return json_encode($json);
	}

	/*=============================================
	              Crear un registo          
	=============================================*/
	
	public function store(Request $request){

		$data = array(
				"first_name"=>$request->input("first_name"), 
				"last_name"=>$request->input("last_name"),
				"first_name"=>$request->input("first_name"),
				"email"=>$request->input("email")	
		);
		if(!empty($data)){


			/*=============================================
			                 Validar datos            
			=============================================*/

			
			$validator = Validator::make($data, [
	            'first_name' => 'required|string|max:255',
	            'last_name' => 'required|string|max:255',
	            'email' => 'required|string|email|unique:clients'
	        ]);

			//si falla la validacion
			
	        if ($validator->fails()) {

	        	$errors = $validator->errors();

				$json = array(
					"status"=>404,
					"detail"=>$errors
				);
				return json_encode($json);
			}else{
				
				//pasa validacion

				$id_client=Hash::make($data["first_name"].$data["last_name"].$data["email"]);
				$secret_key=Hash::make($data["email"].$data["last_name"].$data["first_name"],['rounds'=> 12 ]);

				$client = new Clients();
				$client->first_name = $data["first_name"];
				$client->last_name = $data["last_name"];
				$client->email = $data["email"];
				$client->id_client=str_replace('$','-',$id_client);
				$client->secret_key=str_replace('$','-',$secret_key);
				$client->save();

				$json = array(
					"status"=>200,
					"detail"=>"Registro exitoso, tome sus credenciales y guardelas",
					"credenciales"=>array("id_client"=>str_replace('$','-',$id_client),"secret_key"=>str_replace('$','-',$secret_key))
				);			
				return json_encode($json);
			}
		}else{

			$json = array(
				"status"=>404,
				"detail"=>"Registo con errores"
			);
			return json_encode($json);			
		}
	}
}

