<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MongoDB no disponible</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-6">
    <div class="max-w-lg bg-white border border-slate-200 rounded-2xl p-6 space-y-3">
        <h1 class="text-lg font-bold text-slate-900">MongoDB 7 no está disponible</h1>
        <p class="text-sm text-slate-600">El sistema usa <code>mongodb/laravel-mongodb</code>. Levante el stack con Docker:</p>
        <pre class="text-xs bg-slate-900 text-slate-100 rounded-lg p-3 overflow-x-auto">docker compose up --build</pre>
        <p class="text-[11px] font-mono text-rose-700 break-all">{{ $message }}</p>
    </div>
</body>
</html>
