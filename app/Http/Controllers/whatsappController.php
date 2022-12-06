<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\citacionController;
use App\Models\citaciones;
use Illuminate\Support\Facades\DB;
use App\Models\Mensaje;
use App\Models\trabajadores;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

use function PHPSTORM_META\type;

class whatsappController extends Controller
{
    
    public function mensaje (Request $request){
        $token = env("TOKEN_PERMANENTE");
        
        $nombre = $request->nombre;
        $apellido = $request ->apellido;
        $telefono = $request->telefono;
        $turno = $request -> turno;
        $fecha_citacion = $request -> fecha_citacion;
        
        $data_citacion = [
            "id_trabajador" => $request->id_trabajador,
            "fecha_citacion" => $request-> fecha_citacion,
            "id_turno" => intval($request-> id_turno )
        ];

        app(citacionController::class)->store_citacion($data_citacion);

        $data_trabajador = [
            "id_trabajador" => $request -> id_trabajador,
            "rut" => $request->rut,
            "nombre" => $nombre,
            "apellido" => $apellido,
            "correo" => $request->correo,
            "telefono" => $telefono,
            "id_estado" => 1
        ];
        //Ejecutamos los metodos del controlador de los trabajadores
        app(trabajadorController::class) -> update_trabajador($data_trabajador);
        
       
        $url = env("URL_WTSP");
        
        $response = Http::withHeaders([
            'Authorization'=> 'Bearer '.$token
        ])->post($url,[
            "messaging_product"=> "whatsapp",
            "to"=> "56".$telefono,
            "type"=> "template",
            "template" =>[
                "name"=> "citacion_nombre_turno",
                "language"=>[
                    "code" => "es_AR"
                ],
                "components" =>[
                        [
                        "type"=>"body",
                        "parameters" =>[
                            [
                                "type"=>"text",
                                "text" => "$nombre $apellido"
                            ],
                            [
                                "type"=> "text",
                                "text" => $turno,
                            ],
                            [
                                "type"=>"text",
                                "text" => $fecha_citacion,
                            ]
                         ]
                    ],
                ]  
            ]
        ]);
        $data = json_decode($response->body());
        //Obtnemos el id del mensaje
        $data = $data -> messages[0]->id;
        //Guardamos el mensaje

        $mensaje = new Mensaje();
        $mensaje->id_trabajador = $request->id_trabajador;
        $mensaje->rut = $request -> rut;
        $mensaje -> nombre = $nombre;
        $mensaje -> apellido = $apellido;
        $mensaje -> turno = $turno;
        $mensaje->fecha_citacion = $fecha_citacion;
        $mensaje-> wa_id = $data;
        $mensaje -> respuesta = "Sin respuesta";
        $mensaje-> save();
        
        if($response->status()==200){
            
            return response() -> json([$data,Response::HTTP_OK]) ;
        }else{
            return response(Response::HTTP_NOT_FOUND);
        }        
    }

    public function verify(Request $request){//Funcion para verificar webhook
        
        try{
            $verifyToken = 'intra2022!';
            $query = $request -> query();
            $mode = $query["hub_mode"];
            $token = $query["hub_verify_token"];
            $challenge = $query["hub_challenge"];

            if($mode && $token){
                if($mode=="subscribe" && $token == $verifyToken){
                    return response($challenge,200)-> header('Content-Type',"text/plain");
                }
            }
            throw new Exception("Invalid request");

        }catch(Exception $e){
            return response()->json([
                "success" => false,
                "error" => $e->getMessage()
            ],500);
        }

    }

    public function webhook(Request $request){
        $cambios = $request ->entry[0]["changes"];
        if($cambios){
            $changes = $cambios[0];
            $values = $changes["value"];
            if( array_key_exists("messages",$values) ){
                $messages = $values ["messages"];
                $context = $messages[0]["context"];
                $id_mensaje = $context["id"];
                $type =  $messages[0]["type"]; //Tipo de mensaje
                if($type==="button"){         
                    $obj_mns = $messages[0]["button"];
                    $respuesta =$obj_mns["text"];
                    
                    Log::info(["ID_MENSAJE: "=>$id_mensaje,"Respuesta"=>$respuesta]);
                    //Actualizamos el registro con la respuesta
                    /*
                    $sql = "update mensajes
                    set respuesta='$respuesta' 
                    WHERE wa_id = '$id_mensaje'";
                    DB::update($sql);
                    */
                    Mensaje::where('wa_id',$id_mensaje)->update([
                        "respuesta" => $respuesta
                    ]);
                    
                    return response()->json([
                        "id" => $id_mensaje,
                        "mensaje"=>$respuesta
                    ]);
                }
            }else{
                return response()->json("Esta estructura no coincide");
            }
    
        }
    }
    public function mensajesTrabajador(Request $request){
        $sql = "select * from mensajes where id_trabajador=$request->id order by created_at desc";
      
        $citaciones = DB::select($sql);

        return $citaciones;

    }
   
}
