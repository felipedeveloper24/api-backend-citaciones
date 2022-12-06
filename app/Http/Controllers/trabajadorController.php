<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrabajadorRequest;
use App\Models\trabajadores;
use Illuminate\Http\Request;

class trabajadorController extends Controller
{
    public function index(){
        return trabajadores::all();
    }

    public function store(TrabajadorRequest $request){
        $trabajador = new trabajadores();
        $trabajador -> rut = $request -> rut;
        $trabajador -> nombre = $request -> nombre;
        $trabajador -> apellido = $request -> apellido;
        $trabajador -> correo = $request -> correo;
        $trabajador -> telefono = $request -> telefono;
        $trabajador-> id_estado = 1;
        $trabajador -> save();
        return $trabajador;
    }

    public function show($id){
        $trabajador = trabajadores::find($id);
        return $trabajador;
    }

    public function update(Request $request, $id){
        $trabajador = trabajadores::findOrFail($id);
     
        isset($request->rut) &&  $trabajador -> rut = $request -> rut;
        isset($request->nombre) && $trabajador -> nombre = $request -> nombre;
        isset($request->apellido) && $trabajador -> apellido = $request -> apellido;
        isset($request->correo) && $trabajador -> correo = $request -> correo;
        isset($request->telefono) && $trabajador -> telefono = $request -> telefono;
        isset($request->id_estado) &&$trabajador -> id_estado = $request -> id_estado;
        $trabajador -> save();
        return response([
            "mensaje"=> "Datos actualizados exitosamente",
            "trabajador"=> $trabajador
        ]);
    }
    public function update_trabajador($data_trabajador){
        $trabajador = trabajadores::findOrFail($data_trabajador["id_trabajador"]);
        $trabajador -> rut = $data_trabajador["rut"];
        $trabajador -> nombre = $data_trabajador["nombre"];
        $trabajador -> apellido =$data_trabajador["apellido"];
        $trabajador -> correo = $data_trabajador["correo"];
        $trabajador -> telefono = $data_trabajador["telefono"];
        $trabajador -> id_estado = $data_trabajador["id_estado"];
        $trabajador -> save();
        
        return response([
            "mensaje"=> "trabajador actualizado!",
        ]);
    }

    public function destroy($id){
        trabajadores::destroy($id);

        return response([
            "mensaje"=>"Trabajador eliminado exitosamente"
        ]);
    }

    public function obtenerdata (Request $request){

        return $request;
    }
    public function prueba(){
        return (response()->json(
            [
                "mensaje"=> "Estamos consumiento api de intra!!!!!"
            ]
        ));
    }
}
