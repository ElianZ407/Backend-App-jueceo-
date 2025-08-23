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
        Schema::create('likes', function (Blueprint $table) {
            $table->id();

            // Relación con usuarios
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id')
                  ->references('usuario_id')
                  ->on('usuarios')
                  ->onDelete('cascade');

            // Campos para relación polimórfica (puede ser publicacion o comentario)
            $table->unsignedBigInteger('likeable_id');
            $table->string('likeable_type'); // indica si es Publicacion o Comentario

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
        Schema::dropIfExists('likes');
    }
};
