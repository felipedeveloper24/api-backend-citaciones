<?php

namespace App\Http\Controllers;

use App\Models\trabajadores;
use Illuminate\Http\Request;

class trabajadorController extends Controller
{
    public function index(){
        return trabajadores::all();
    }

    public function store(Request $request){
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

        $trabajador -> rut = $request -> rut;
        $trabajador -> nombre = $request -> nombre;
        $trabajador -> apellido = $request -> apellido;
        $trabajador -> correo = $request -> correo;
        $trabajador -> telefono = $request -> telefono;
        $trabajador -> id_estado = $request -> id_estado;
        $trabajador -> save();
        return response([
            "mensaje"=> "Datos actualizados exitosamente",
            "trabajador"=> $trabajador
        ]);
    }

    public function destroy($id){
        trabajadores::destroy($id);

        return response([
            "mensaje"=>"Trabajador eliminado exitosamente"
        ]);
    }

}
