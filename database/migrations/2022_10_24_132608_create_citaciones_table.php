<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('citaciones', function (Blueprint $table) {
            $table->id();
            $table->date("fecha_citacion");
            $table -> unsignedBigInteger("id_trabajador");

            $table -> foreign("id_trabajador")
            -> references("id")
            -> on("trabajadores")
            -> cascadeOnUpdate()
            -> cascadeOnDelete();

            $table->unsignedBigInteger("id_turno");
            $table-> foreign("id_turno")
            -> references("id") 
            -> on("turnos") 
            -> cascadeOnDelete()
            -> cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('citaciones');
    }
};
