<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo/SUMORROW-LOGO-M.png') }}">
    <title>@yield('title', 'Sumorrow Admin')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Bricolage Grotesque', sans-serif; }
        
        /* Custom Scrollbar for a cleaner look */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #6EA1B2; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #4D8EA2; }
    </style>
    <!-- Alpine.js to handle mobile interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-morning-mist/20 antialiased text-deep-midnight" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" 
             class="fixed inset-0 z-20 bg-deep-midnight/50 transition-opacity lg:hidden" 
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
               class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-morning-mist flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">
            
            <div class="p-6 md:p-8 flex items-center justify-between">
                <div>
                    {{-- <h2 class="text-2xl font-extrabold text-deep-midnight tracking-tight flex items-center gap-2"> --}}
                        <img src="{{ asset('images/logo/SUMORROW-LOGO-BLACK.png') }}"  alt="Logo" onerror="this.style.display='none'"> 
                    {{-- </h2> --}}
                </div>
                <!-- Close sidebar (Mobile) -->
                <button @click="sidebarOpen = false" class="lg:hidden text-blue-bird hover:text-deep-midnight">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="grow px-4 pb-4 space-y-1 overflow-y-auto">
                <p class="px-4 text-xs font-semibold text-blue-bird uppercase tracking-wider mb-2 mt-4">Main Menu</p>
                
                <a href="{{ route('admin.dashboard') }}" @class([
                    'group flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors',
                    'font-bold bg-sky-oxygen text-deep-midnight' => request()->routeIs('admin.dashboard'),
                    'font-semibold text-blue-bird hover:bg-morning-mist/30 hover:text-deep-midnight' => !request()->routeIs('admin.dashboard'),
                ])>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-75" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Dashboard
                </a>
                
                <a href="{{ route('admin.forum-moderation') }}" @class([
                    'group flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors',
                    'font-bold bg-sky-oxygen text-deep-midnight' => request()->routeIs('admin.forum-moderation'),
                    'font-semibold text-blue-bird hover:bg-morning-mist/30 hover:text-deep-midnight' => !request()->routeIs('admin.forum-moderation'),
                ])>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-bird group-hover:text-summit-blue transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                    </svg>
                    Forum Moderation
                </a>
                
                <a href="{{ route('admin.user-updates') }}" @class([
                    'group flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors',
                    'font-bold bg-sky-oxygen text-deep-midnight' => request()->routeIs('admin.user-updates'),
                    'font-semibold text-blue-bird hover:bg-morning-mist/30 hover:text-deep-midnight' => !request()->routeIs('admin.user-updates'),
                ])>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-bird group-hover:text-summit-blue transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    User Updates
                </a>
                
                <a href="{{ route('admin.mountain-data') }}" @class([
                    'group flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors',
                    'font-bold bg-sky-oxygen text-deep-midnight' => request()->routeIs('admin.mountain-data'),
                    'font-semibold text-blue-bird hover:bg-morning-mist/30 hover:text-deep-midnight' => !request()->routeIs('admin.mountain-data'),
                ])>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-bird group-hover:text-summit-blue transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Mountain Data
                </a>
            </nav>

            <div class="p-4 border-t border-morning-mist pb-6 mb-safe">
                <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-morning-mist/30 transition-colors cursor-pointer">
                    <div class="w-10 h-10 rounded-full bg-summit-blue flex items-center justify-center text-white text-sm font-bold shadow-sm">
                        {{ strtoupper(substr(Auth::user()->username ?? 'A', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-deep-midnight truncate">{{ Auth::user()->username ?? 'Administrator' }}</p>
                        <p class="text-xs text-blue-bird truncate">{{ Auth::user()->email ?? 'admin@sumorrow.id' }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Top Header -->
            <header class="bg-white border-b border-morning-mist h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0">
                <div class="flex items-center gap-4">
                    <!-- Hamburger Menu Button -->
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 text-blue-bird hover:text-deep-midnight hover:bg-morning-mist/30 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <!-- Page Summary/Breadcrumbs -->
                    <h1 class="text-lg font-bold text-deep-midnight hidden sm:block">@yield('page_title', 'Dashboard')</h1>
                </div>

                <div class="flex items-center gap-4">
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="group flex items-center gap-2 px-5 py-2.5 rounded-full bg-morning-mist/20 text-sm font-bold text-summit-blue hover:bg-summit-blue hover:text-white transition-all duration-300 shadow-sm active:scale-95">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <!-- Main Page Content -->
            <main class="flex-1 overflow-y-auto w-full p-4 sm:p-6 lg:p-8">
                <!-- Start Content -->
                <div class="max-w-7xl mx-auto">
                    @yield('admin_content')
                </div>
                <!-- End Content -->
            </main>
        </div>
    </div>
</body>
</html>