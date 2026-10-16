<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Crea el esquema de posts. Laravel registra esta migración para no repetirla. */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id(); // Clave primaria autoincremental.
            $table->string('title'); // VARCHAR requerido; título del post.
            $table->text('body'); // Texto requerido para el contenido.
            $table->timestamps(); // Crea created_at y updated_at.
        });
    }

    /** Revierte up() eliminando la tabla; rollback borra sus datos. */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
