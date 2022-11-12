<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use League\CommonMark\Reference\Reference;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trabajadores', function (Blueprint $table) {
            $table->id();
            $table-> string("rut");
            $table->string("nombre");
            $table->string("apellido");
            $table->string("correo");
            $table->integer("telefono");
            $table->unsignedBigInteger("id_estado");
            $table-> foreign("id_estado") -> references("id") -> on("estados")
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
        Schema::dropIfExists('trabajadores');
    }
};
