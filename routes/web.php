<?php

use App\Http\Controllers\citacionController;
use App\Http\Controllers\citacionesController;
use App\Http\Controllers\trabajadorController;
use App\Http\Controllers\turnosController;
use App\Http\Controllers\whatsappController;
use App\Models\citacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::controller(trabajadorController::class) -> group(function(){
    Route::get("/trabajadores","index");
    Route::post("/trabajador","store");
    Route::put('/trabajador/{id}',"update");
    Route::get("/trabajador/{id}","show");
    Route::delete("/trabajador/{id}","destroy");
    Route::post("/data","obtenerdata");
    Route::get("/test","prueba");
});

Route::controller(turnosController::class) -> group(function(){
    Route::get("/turnos","show");
    Route::get("/turno/{id}","getName");
});

Route::controller(citacionController::class) -> group(function(){
    Route::post("/citacion","store");
    Route::get("/citaciones/{id}","join");
});
Route::controller(whatsappController::class) -> group(function(){
    Route::post("/mensaje","mensaje");
    Route::get("/verify","verify");
    Route::post("/webhook","webhook");
   
    Route::get("/mensajes/{id}","mensajesTrabajador");
});
