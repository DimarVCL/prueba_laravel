<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Companies;
use App\Clients;
use Illuminate\Support\Facades\Validator;

class CompaniesController extends Controller
{
	/*=============================================
	 Mostrar todos las empresas para usuario autorizado       
	=============================================*/

	
    public function index(Request $request){

    	$token = $request->header('Authorization');
    	$clients = Clients::all();
    	$json = array();

    	foreach ($clients as $key => $value){

    		if("Basic ".base64_encode($value["id_client"].":".$value["secret_key"]) == $token){

		    	$companies = Companies::all();

		    	if(!empty($companies)){

					$json = array(
						"status"=>200,
						"reg total"=>count($companies),
						"details"=>$companies
					);			
					return json_encode($json,true);
				}else{

					$json = array(
						"status"=>200,
						"reg total"=>0,
						"details"=>"no hay ninguna empresa registrada"
					);			
					return json_encode($json,true);
				}
    		}else{
				$json = array(
					"status"=>404,
					"details"=>"no esta autorizado para recibir los registros9"
				);   			
    		}		
    	}
		return json_encode($json,true);
	}
	/*=============================================
	 Crear un registro       
	=============================================*/
	public function store(Request $request){

		$token = $request->header('Authorization');
    	$clients = Clients::all();
    	$json = array();

    	foreach ($clients as $key => $value){
    		if("Basic ".base64_encode($value["id_client"].":".$value["secret_key"]) == $token){

    			$data = array(
				"name"=>$request->input("name"), 
				"num_workers"=>$request->input("num_workers"),
				"date_create"=>$request->input("date_create"),
				"type"=>$request->input("type")
				);

				if(!empty($data)){

					$validator = Validator::make($data, [
	            		"name" => "required|string|max:255|unique:companies",
	             		"num_workers" => "required|numeric",
	            		"date_create" => "required|date|date_format:Y-m-d",
	            		"type" =>  "required|numeric"
	        		]);	

	        		if($validator->fails()){
	        			
	        			$errors = $validator->errors();

	        			$json = array(
							"status"=>404,
							"details"=>$errors
						); 
	        		}else{

	        			$companies = new Companies();
	        			$companies->name=$data["name"];
	        			$companies->num_workers=$data["num_workers"];
	        			$companies->date_create=$data["date_create"];
	        			$companies->type=$data["type"];
	        			$companies->id_creator=$value["id"];
	        			

	        			$companies->save();

	        			$json = array(
							"status"=>200,
							"details"=>"Registro exitoso, su empresa ha sido guardada"
						); 

	        			return json_encode($json,true);
	        		}
				}else{

					$json = array(
						"status"=>404,
						"details"=>"Los registros no puedem estar vacios"
					);  
				}
    		}
    	}
    	return json_encode($json,true);
	}

	/*=============================================
	 tomar un registro       
	=============================================*/


	public function show($id, Request $request){

		$token = $request->header('Authorization');
    	$clients = Clients::all();
    	$json = array();

    	foreach ($clients as $key => $value){

    		if("Basic ".base64_encode($value["id_client"].":".$value["secret_key"]) == $token){

    			$company = Companies::where("id",$id)->get();

		    	if(sizeof($company)>0){

					$json = array(
						"status"=>200,
						"details"=>$company
					);			
					return json_encode($json,true);
				}else{

					$json = array(
						"status"=>200,
						"reg total"=>0,
						"details"=>"La empresa no esta registrada"
					);			
				}
    		}else{

				$json = array(
					"status"=>404,
					"details"=>"no esta autorizado para recibir los registros"
				);    			
    		}
    	}

    	return json_encode($json,true);
	}

	/*=============================================
	 Editar un registro       
	=============================================*/
	public function update($id,Request $request){

		$token = $request->header('Authorization');
    	$clients = Clients::all();
    	$json = array();

    	foreach ($clients as $key => $value){

    		if("Basic ".base64_encode($value["id_client"].":".$value["secret_key"]) == $token){

    			$data = array(
					"name"=>$request->input("name"), 
					"num_workers"=>$request->input("num_workers"),
					"date_create"=>$request->input("date_create"),
					"type"=>$request->input("type")
				);

				if(!empty($data)){

					$validator = Validator::make($data, [
	            		"name" => "required|string|max:255",
	             		"num_workers" => "required|numeric",
	            		"date_create" => "required|date|date_format:Y-m-d",
	            		"type" => "required|numeric"
	        		]);	

	        		if($validator->fails()){
	        			
	        			$errors = $validator->errors();

	        			$json = array(
							"status"=>404,
							"details"=>$errors
						); 
	        		}else{

	        			$bring_company = Companies::where("id",$id)->get();

	        			if($value["id"] == $bring_company[0]["id_creator"]){

	        				$data = array(
	        					"name"=>$data["name"],
	        					"num_workers"=>$data["num_workers"],
	        					"date_create"=>$data["date_create"],
	        					"type"=>$data["type"]
	        				);

							$companies = Companies::where("id",$id)->update($data);

	        				$json = array(
								"status"=>200,
								"details"=>"Registro exitoso, su empresa ha sido actualizada"
							); 

	        				return json_encode($json,true);

	        			}else{

	        				$json = array(
								"status"=>404,
								"details"=>"No esta autorizado a modificar esta empresa"
							);

							return json_encode($json,true);  
	        			}
	        		}
				}else{

					$json = array(
						"status"=>404,
						"details"=>"Los registros no puedem estar vacios"
					);  
				}
    		}
    	}

    	return json_encode($json,true);
	}
	/*=============================================
	 Eliminar un registro       
	=============================================*/
		public function destroy($id, Request $request){

		$token = $request->header('Authorization');
    	$clients = Clients::all();
    	$json = array();

    	foreach ($clients as $key => $value){

    		if("Basic ".base64_encode($value["id_client"].":".$value["secret_key"]) == $token){

    			$validate = Companies::where("id", $id)->get();

    			if(!empty($validate)){

    				if($value["id"] == $validate[0]["id_creator"]){

    					$company = Companies::where("id", $id)->delete();

    					$json = array(

							"status"=>200,
							"details"=>"Se ha borrado su empresa con exito"
						);

						return json_encode($json,true); 
    				}else{

        				$json = array(
							"status"=>404,
							"details"=>"No esta autorizado a modificar esta empresa"
						);

						return json_encode($json,true);  
					}

    			}else{

					$json = array(
						"status"=>404,
						"details"=>"El curso no existe"
					); 

					return json_encode($json,true); 
    			}
    		}
    	}

    	return json_encode($json,true);
	}

}
