<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="icon" type="image/png" href="{{ asset('images/logo/SUMORROW-LOGO-M.png') }}">
    <title>Sumorrow - Summit Tommorow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&family=Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    @vite (['resources/css/app.css','resources/js/app.js'])
</head>
<body
    class="bg-[#E7E7E7] antialiased font-sans text-gray-900 flex flex-col min-h-screen"
>
    @if (request()->is('/'))
        <x-navbar />
    @else
        <x-navbar-light />
    @endif

    <main class="flex-grow">
        @yield ('content')
    </main>

    <x-footer />
</body>
</html>
