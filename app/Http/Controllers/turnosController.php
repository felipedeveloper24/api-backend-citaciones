<?php

namespace App\Http\Controllers;

use App\Models\turnos;
use Illuminate\Http\Request;

class turnosController extends Controller
{
    public function show(){
        return turnos::all();
    }
    public function getName($id){
        $turno = turnos::find($id);
        return $turno;
    }
}
