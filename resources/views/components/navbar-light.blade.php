<nav class="fixed top-6 left-0 right-0 mx-auto w-[95%] max-w-350 rounded-2xl z-50 flex flex-col bg-white/85 border border-white/60 px-6 py-4 text-[#1a2b4c] backdrop-blur-xl shadow-lg shadow-gray-200/50 transition-all duration-300">
    <div class="flex flex-row justify-between items-center w-full relative">

        <div class="hidden md:flex flex-1 text-l font-bold gap-2">
            <a href="/" class="px-4 py-2 rounded-lg transition text-slate-600 hover:bg-[#094174]/10 hover:text-[#094174]">Home</a>
            <a href="/explore" class="px-4 py-2 rounded-lg transition text-slate-600 hover:bg-[#094174]/10 hover:text-[#094174]">Explore</a>
            <a href="#" class="px-4 py-2 rounded-lg transition text-slate-600 hover:bg-[#094174]/10 hover:text-[#094174]">Community</a>
        </div>

        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex justify-center z-10 w-fit pointer-events-none">
            <a href="/" class="pointer-events-auto">
                <img
                    src="{{ asset('images/logo/SUMORROW-LOGO-BLACK.png') }}"
                    alt="Sumorrow Logo"
                    class="h-10 md:h-12 w-auto transition-transform hover:scale-105"
                />
            </a>
        </div>

        <div class="hidden md:flex flex-1 justify-end">
            @auth
                <div class="relative group">
                    <button class="flex items-center focus:outline-none transition-transform hover:scale-105">
                        @php
                            $avatar = Auth::user()->avatar_url;
                            $src = $avatar ? (str_contains($avatar, 'http') ? $avatar : asset('storage/' . $avatar)) : null;
                            $defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode(substr(Auth::user()->username, 0, 2)) . '&background=random';
                        @endphp
                        <img src="{{ $avatar ? $src : $defaultAvatar }}" alt="Profile"
                            class="h-11 w-11 rounded-full object-cover border-2 border-gray-200 group-hover:border-[#094174]/40 transition-colors shadow-sm">
                    </button>
                    <!-- Improved dropdown for light navbar -->
                    <div class="absolute right-0 mt-3 w-56 bg-white/95 border border-gray-200/80 rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 backdrop-blur-lg transform origin-top-right group-hover:scale-100 scale-95 z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                            <p class="text-sm font-bold text-[#1a2b4c] truncate">{{ Auth::user()->username }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="p-2">
                            <a href="{{ route('profile') }}" class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-[#094174]/10 hover:text-[#094174] transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Profile
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="mt-1 border-t border-gray-100 pt-1">
                                @csrf
                                <button type="submit" class="flex items-center w-full text-left px-3 py-2.5 text-sm font-medium text-red-500 rounded-lg hover:bg-red-50 hover:text-red-700 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Log out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('showLogin') }}" class="px-8 py-3 bg-[#094174] text-white font-bold rounded-full shadow-md transition hover:bg-[#105DA3] hover:shadow-lg hover:-translate-y-0.5">Log in</a>
            @endauth
        </div>

        <button id="hamburger-btn-light" class="md:hidden ml-auto text-[#1a2b4c] focus:outline-none relative z-20 p-2 -mr-2 rounded-lg hover:bg-[#094174]/10 transition">
            <svg class="w-7 h-7 transition-transform duration-300" id="hamburger-icon-light" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

    </div>

    <div id="mobile-menu-light" class="w-full md:hidden transition-all duration-500 ease-in-out max-h-0 opacity-0 overflow-hidden">
        <div class="flex flex-col gap-4 mt-6 pt-4 border-t border-gray-200/60 text-center pb-2">
            <a href="/" class="font-semibold py-2 rounded-lg text-slate-600 hover:bg-[#094174]/10 hover:text-[#094174] transition">Home</a>
            <a href="/explore" class="font-semibold py-2 rounded-lg text-slate-600 hover:bg-[#094174]/10 hover:text-[#094174] transition">Explore</a>
            <a href="#" class="font-semibold py-2 rounded-lg text-slate-600 hover:bg-[#094174]/10 hover:text-[#094174] transition">Community</a>
            <div class="pt-4 border-t border-gray-200/60 flex flex-col gap-2">
                @auth
                    <p class="text-sm text-gray-500 mb-2">Logged in as {{ Auth::user()->username }}</p>
                    <form action="{{ route('logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="font-bold text-red-500 py-2 rounded-lg hover:bg-red-50 transition w-full">Log out</button>
                    </form>
                @else
                    <a href="{{ route('showLogin') }}" class="mt-2 px-8 py-3 bg-[#094174] text-white font-bold rounded-full shadow-md hover:shadow-lg transition hover:bg-[#105DA3] mx-auto w-fit">Log in</a>
                @endauth
            </div>
        </div>
    </div>

</nav>

<script>
    document.addEventListener('DOMContentLoaded',() =>{
        const btn = document.getElementById('hamburger-btn-light');
        const menu = document.getElementById('mobile-menu-light');
        const icon = document.getElementById('hamburger-icon-light');

        btn.addEventListener('click',() => {
            const isClosed = menu.classList.contains('max-h-0');

            if (isClosed) {
                // Open menu
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
                menu.classList.remove('max-h-0', 'opacity-0');
                menu.classList.add('max-h-[400px]', 'opacity-100');
            } else {
                // Close menu
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
                menu.classList.remove('max-h-[400px]', 'opacity-100');
                menu.classList.add('max-h-0', 'opacity-0');
            }
        });
    });
</script>
