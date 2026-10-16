<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent asociado por convención con la tabla `posts`.
 * Eloquent usa la llave `id` y administra created_at/updated_at.
 */
class Post extends Model
{
    // Permite crear datos de prueba con factories y HasFactory en tests.
    use HasFactory;

    // Protección contra asignación masiva: create()/update() solo aceptan
    // estos campos. El id y las fechas los administra la base/framework.
    protected $fillable = ['title', 'body'];
}
