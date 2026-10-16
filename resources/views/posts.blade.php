<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel Interview Starter</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 760px; margin: 3rem auto; padding: 0 1rem; color: #17202a; }
        code { background: #f0f2f5; padding: .15rem .35rem; border-radius: .25rem; }
        li { margin: .65rem 0; }
    </style>
</head>
<body>
    <h1>Laravel MVC + API starter</h1>
    <p>La app está lista. El ejemplo persiste datos con Eloquent y SQLite.</p>
    <p>Endpoints: <code>GET/POST /api/posts</code>, <code>GET/PATCH/DELETE /api/posts/{id}</code>.</p>
    <h2>Posts guardados ({{ $posts->count() }})</h2>
    <ul>
        @forelse ($posts as $post)
            <li><strong>{{ $post->title }}</strong> — {{ $post->body }}</li>
        @empty
            <li>Aún no hay posts. Crea uno con la API.</li>
        @endforelse
    </ul>
</body>
</html>
