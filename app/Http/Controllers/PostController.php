<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PostController extends Controller
{
    /**
     * Lee los posts más recientes y devuelve hasta 10 por página.
     * PostResource convierte cada modelo a JSON y Laravel agrega metadatos
     * de paginación (total, página actual y enlaces).
     */
    public function index(): AnonymousResourceCollection
    {
        return PostResource::collection(Post::query()->latest()->paginate(10));
    }

    /**
     * Valida el JSON recibido y agrega un post.
     * Laravel responde automáticamente con 422 si la validación falla;
     * si se crea correctamente, el recurso se devuelve con HTTP 201.
     */
    public function store(Request $request): JsonResponse
    {
        // Solo se guardan los campos validados, nunca se pasa todo el request
        // sin filtrar al modelo.
        $post = Post::create($request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]));

        return (new PostResource($post))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Devuelve un post individual como JSON.
     * Laravel resuelve {post} buscando su ID mediante route model binding;
     * si no existe, responde 404 automáticamente.
     */
    public function show(Post $post): PostResource
    {
        return new PostResource($post);
    }

    /**
     * Edita total o parcialmente el post recibido en la URL.
     * La regla sometimes permite omitir un campo en PATCH; si se incluye,
     * sigue siendo obligatorio y debe cumplir el resto de sus reglas.
     */
    public function update(Request $request, Post $post): PostResource
    {
        $post->update($request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'body' => ['sometimes', 'required', 'string'],
        ]));

        // refresh vuelve a leer el registro guardado antes de convertirlo a JSON.
        return new PostResource($post->refresh());
    }

    /**
     * Elimina el registro y responde HTTP 204 (operación exitosa sin body).
     * El modelo llega desde la URL y Laravel responde 404 si no existe.
     */
    public function destroy(Post $post): Response
    {
        $post->delete();

        return response()->noContent();
    }
}
