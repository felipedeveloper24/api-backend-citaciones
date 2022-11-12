<?php

namespace App\Http\Controllers;

use App\Models\citaciones;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class citacionController extends Controller
{
    public function index(){
        return citaciones::all();
    }
    public function join($id){

        $sql = "select c.fecha_citacion, tu.nombre_turno from turnos tu, trabajadores t, citaciones c
        where c.id_turno = tu.id
        and c.id_trabajador = t.id 
        and t.id = $id order by c.created_at desc ";
        $citaciones = DB::select($sql);
        return $citaciones;

    }
    public function store(Request $request){
        $citacion = new citaciones();
        $citacion -> fecha_citacion = $request -> fecha_citacion;
        $citacion -> id_trabajador = $request -> id_trabajador;
        $citacion -> id_turno = $request -> id_turno;
        $citacion -> save();

        return $citacion;
    }
}
