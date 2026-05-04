<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link
        rel="icon"
        type="image/png"
        href="{{ asset('images/logo/SUMORROW-LOGO-M.png') }}"
    />
    <title>Sumorrow - Summit Tommorow</title>
    @vite (['resources/css/app.css','resources/js/app.js'])
</head>
<body
    class="bg-[#E7E7E7] antialiased font-sans text-gray-900 flex flex-col min-h-screen"
>
    <x-navbar />
    <main class="flex-grow">
        @yield ('content')
    </main>
    <x-footer />
</body>
</html>
