<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo/SUMORROW-LOGO-M.png') }}">
    <title>@yield('title', 'Sumorrow Admin')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js to handle mobile interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="admin-layout bg-[#F3F7FA] antialiased text-deep-midnight" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">

        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" x-cloak
             class="fixed inset-0 z-20 bg-deep-midnight/60 transition-opacity lg:hidden"
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
        </div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="topo-bg fixed inset-y-0 left-0 z-30 w-64 bg-deep-midnight flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">

            <div class="px-6 pt-7 pb-5 flex items-center justify-between">
                <a href="{{ route('admin.dashboard') }}" class="block">
                    <img src="{{ asset('images/logo/SUMORROW-LOGO-WHITE.png') }}" alt="Sumorrow" class="h-7 w-auto" onerror="this.style.display='none'">
                    <span class="mt-1.5 block text-[10px] font-bold uppercase tracking-[0.25em] text-sky-oxygen/80">{{ __('admin.sidebar_title') }}</span>
                </a>
                <!-- Close sidebar (Mobile) -->
                <button @click="sidebarOpen = false" class="lg:hidden text-morning-mist/60 hover:text-white" aria-label="{{ __('admin.close_menu') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="grow px-3 pb-4 space-y-6 overflow-y-auto">
                <div>
                    <p class="px-4 text-[10px] font-bold text-morning-mist/40 uppercase tracking-[0.2em] mb-2 mt-2">{{ __('admin.nav_overview') }}</p>

                    <a href="{{ route('admin.dashboard') }}" @class([
                        'group relative flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors',
                        'font-bold bg-white/10 text-white' => request()->routeIs('admin.dashboard'),
                        'font-semibold text-morning-mist/70 hover:bg-white/5 hover:text-white' => !request()->routeIs('admin.dashboard'),
                    ])>
                        @if (request()->routeIs('admin.dashboard'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-sky-oxygen"></span>
                        @endif
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        {{ __('admin.nav_dashboard') }}
                    </a>
                </div>

                <div>
                    <p class="px-4 text-[10px] font-bold text-morning-mist/40 uppercase tracking-[0.2em] mb-2">{{ __('admin.nav_management') }}</p>

                    <a href="{{ route('admin.user-updates') }}" @class([
                        'group relative flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors',
                        'font-bold bg-white/10 text-white' => request()->routeIs('admin.user-updates'),
                        'font-semibold text-morning-mist/70 hover:bg-white/5 hover:text-white' => !request()->routeIs('admin.user-updates'),
                    ])>
                        @if (request()->routeIs('admin.user-updates'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-sky-oxygen"></span>
                        @endif
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        {{ __('admin.nav_user_updates') }}
                    </a>

                    <a href="{{ route('admin.mountain-data') }}" @class([
                        'group relative flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors',
                        'font-bold bg-white/10 text-white' => request()->routeIs('admin.mountain-data', 'admin.mountains.*'),
                        'font-semibold text-morning-mist/70 hover:bg-white/5 hover:text-white' => !request()->routeIs('admin.mountain-data', 'admin.mountains.*'),
                    ])>
                        @if (request()->routeIs('admin.mountain-data', 'admin.mountains.*'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-sky-oxygen"></span>
                        @endif
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('admin.nav_mountain_data') }}
                    </a>
                </div>
            </nav>

            <div class="p-4 border-t border-white/10">
                <div class="flex items-center gap-3 p-2">
                    @if (Auth::user()?->avatar_url)
                        <img src="{{ Auth::user()->avatar_url }}" alt="{{ __('common.avatar_alt') }}" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'" class="w-10 h-10 rounded-full border border-white/20">
                    @else
                        <div class="w-10 h-10 rounded-full bg-summit-blue flex items-center justify-center text-white text-sm font-bold shadow-sm">
                            {{ strtoupper(substr(Auth::user()->username ?? 'A', 0, 1)) }}
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-white truncate">{{ Auth::user()->username ?? __('admin.administrator_fallback') }}</p>
                        <p class="text-xs text-morning-mist/60 truncate">{{ Auth::user()->email ?? '' }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <!-- Top Header -->
            <header class="bg-white/80 backdrop-blur border-b border-morning-mist/60 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0">
                <div class="flex items-center gap-4">
                    <!-- Hamburger Menu Button -->
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 text-blue-bird hover:text-deep-midnight hover:bg-morning-mist/30 rounded-lg" aria-label="{{ __('admin.open_menu') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-base font-bold text-deep-midnight hidden sm:block">@yield('page_title', __('admin.nav_dashboard'))</h1>
                </div>

                <div class="flex items-center gap-4">
                    <nav data-locale-switcher aria-label="{{ __('common.language_switcher') }}" class="inline-flex items-center gap-1 text-xs font-bold">
                        <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}"
                            aria-label="{{ __('common.switch_to_english') }}"
                            @if (app()->isLocale('en')) aria-current="page" @endif
                            @class([
                                'rounded-md px-2 py-1 transition-colors',
                                'bg-summit-blue/10 text-summit-blue' => app()->isLocale('en'),
                                'text-blue-bird hover:bg-summit-blue/10 hover:text-summit-blue' => ! app()->isLocale('en'),
                            ])>EN</a>
                        <span class="text-morning-mist" aria-hidden="true">|</span>
                        <a href="{{ request()->fullUrlWithQuery(['lang' => 'id']) }}"
                            aria-label="{{ __('common.switch_to_indonesian') }}"
                            @if (app()->isLocale('id')) aria-current="page" @endif
                            @class([
                                'rounded-md px-2 py-1 transition-colors',
                                'bg-summit-blue/10 text-summit-blue' => app()->isLocale('id'),
                                'text-blue-bird hover:bg-summit-blue/10 hover:text-summit-blue' => ! app()->isLocale('id'),
                            ])>ID</a>
                    </nav>
                    <form method="POST" action="{{ route('logout') }}" class="inline confirm-submit-form" data-confirm-title="{{ __('common.confirm_logout_title') }}" data-confirm-message="{{ __('common.confirm_logout_message') }}" data-confirm-label="{{ __('common.confirm_logout_label') }}" data-confirm-variant="danger">
                        @csrf
                        <button type="submit" class="group flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-blue-bird hover:bg-morning-mist/30 hover:text-summit-blue transition-colors focus-visible:outline-2 focus-visible:outline-summit-blue">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            {{ __('common.log_out') }}
                        </button>
                    </form>
                </div>
            </header>

            <!-- Main Page Content -->
            <main class="flex-1 overflow-y-auto w-full p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    @if (session('success'))
                        <div x-data="{ show: true }" x-show="show" class="mb-6 flex items-start justify-between gap-4 px-5 py-4 rounded-2xl bg-glacial-teal/10 border border-glacial-teal/30 text-sm font-semibold text-deep-midnight">
                            <div class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-glacial-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ session('success') }}</span>
                            </div>
                            <button @click="show = false" class="text-glacial-teal hover:text-deep-midnight" aria-label="{{ __('common.dismiss') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div x-data="{ show: true }" x-show="show" class="mb-6 flex items-start justify-between gap-4 px-5 py-4 rounded-2xl bg-red-50 border border-red-200 text-sm font-semibold text-red-700">
                            <div class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ session('error') }}</span>
                            </div>
                            <button @click="show = false" class="text-red-400 hover:text-red-700" aria-label="{{ __('common.dismiss') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    @endif

                    @yield('admin_content')
                </div>
            </main>
        </div>
    </div>

    <x-confirm-submit-modal />
</body>
</html>
