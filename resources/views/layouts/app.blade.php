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
    {{-- @if (request()->routeIs('home') || request()->is('/'))
        <x-navbar />
    @else --}}
        <x-navbar-light />
    {{-- @endif --}}

    @if (session('warning'))
        <div
            id="flash-warning"
            role="alert"
            class="fixed top-24 left-1/2 -translate-x-1/2 z-60 w-[92%] max-w-md bg-white border border-amber-200 rounded-2xl shadow-lg shadow-amber-100/60 px-5 py-4 flex items-start gap-3 transition-all duration-300"
        >
            <div class="shrink-0 w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-[#001E3A] mb-0.5">Login required</p>
                <p class="text-sm text-gray-500 leading-relaxed">{{ session('warning') }}</p>
                <div class="flex items-center gap-3 mt-3">
                    <a href="{{ route('showLogin') }}" class="text-xs font-bold bg-[#094174] hover:bg-[#105DA3] text-white px-4 py-1.5 rounded-full transition shadow-sm">Log in</a>
                    <a href="{{ route('register') }}" class="text-xs font-bold text-[#094174] hover:text-[#105DA3] transition">Register</a>
                </div>
            </div>
            <button
                type="button"
                onclick="document.getElementById('flash-warning').remove()"
                aria-label="Dismiss"
                class="shrink-0 text-gray-400 hover:text-[#1a2b4c] transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <script>
            setTimeout(() => {
                const el = document.getElementById('flash-warning');
                if (!el) return;
                el.classList.add('opacity-0', '-translate-y-2');
                setTimeout(() => el.remove(), 300);
            }, 6000);
        </script>
    @endif

    <main class="grow">
        @yield ('content')
    </main>

    <x-footer />
</body>
</html>
