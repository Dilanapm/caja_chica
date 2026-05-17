<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 — Error del servidor | Caja Chica</title>
    @vite(['resources/css/app.css'])
</head>
<body class="h-full bg-slate-50 flex items-center justify-center px-4">
    <div class="text-center max-w-md">
        <p class="text-6xl font-bold text-red-500">500</p>
        <h1 class="mt-4 text-2xl font-semibold text-slate-800">Error del servidor</h1>
        <p class="mt-2 text-sm text-slate-500">Ocurrió un error inesperado. Por favor intenta nuevamente más tarde.</p>
        <a href="{{ url('/') }}" class="mt-6 inline-block rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
            Volver al inicio
        </a>
    </div>
</body>
</html>
