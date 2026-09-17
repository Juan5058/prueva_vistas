<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sistema TRD')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen w-screen overflow-hidden text-slate-100 antialiased" style="background-image: url('/images/fondo-lg.jpg'); background-size: 100% 100%; background-position: center; background-repeat: no-repeat; margin: 0; padding: 0;">
    {{ $slot }}
</body>
</html>
