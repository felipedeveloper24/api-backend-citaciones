<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\citacionController;
use App\Models\citaciones;
use Illuminate\Support\Facades\DB;
use App\Models\Mensaje;
use App\Models\trabajadores;
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
        $mode = $request->query["hub.mode"];
        $token = $request->query["hub.verify_token"];
        $challenge = $request->query["hub.challenge"];
      
        if ($mode && $token) {
          
          if ($mode === "subscribe" && $token === "felipeopazo") {
                return(response()-> json([
                    "mensaje"=>"Webhook verificado".$challenge,
                    Response::HTTP_OK
                ]));
          } else {
                return response()->json([
                    Response::HTTP_NOT_FOUND
                ]);
          }
        }
    }

    public function webhook(Request $request){
        Log::info(["whatsapp ->"=>$request->all()]);
        //Cuando obtenemos la respuesta del mensaje
        //esta función debera descomponer el objeto para obtener el id del mensaje
        //al obtener esto se hace una consulta sql preguntando por el id 
        return $request;
    }
    public function mensajesTrabajador(Request $request){
        $sql = "select * from mensajes where id_trabajador=$request->id order by created_at desc";
        Log::info(["el request--> " => $request->all()]);
        $citaciones = DB::select($sql);

        return $citaciones;

    }
   
}
