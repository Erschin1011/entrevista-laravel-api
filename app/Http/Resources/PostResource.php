<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Define la forma pública del JSON para un post.
     * Centralizar la salida evita exponer accidentalmente otros atributos
     * del modelo si en el futuro se agregan campos privados.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // Identificador usado en las rutas /api/posts/{post}.
            'id' => $this->id,
            // Datos editables que la API permite leer y escribir.
            'title' => $this->title,
            'body' => $this->body,
            // Laravel serializa estos valores DateTime a formato JSON/ISO.
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
