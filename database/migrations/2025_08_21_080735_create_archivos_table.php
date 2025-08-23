<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('archivos', function (Blueprint $table) {
            $table->id(); // id autoincremental
            $table->string('tipo'); // Imagen o video
            $table->unsignedBigInteger('usuario_id'); // referencia al usuario que subió el archivo
            $table->string('ruta')->nullable(); // ruta o nombre del archivo

            $table->timestamps(); // created_at y updated_at

            // Llave foránea hacia users
            $table->foreign('usuario_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archivos');
    }
};
