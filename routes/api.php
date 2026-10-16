<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

// Registra los cinco endpoints REST estándar para posts y los conecta
// con los métodos index, store, show, update y destroy del controlador.
// La URL /api se agrega automáticamente porque estas rutas están en api.php.
Route::apiResource('posts', PostController::class);
