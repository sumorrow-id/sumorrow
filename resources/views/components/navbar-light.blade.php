<nav class="fixed top-6 left-0 right-0 mx-auto w-[95%] max-w-[1400px] rounded-2xl z-50 flex flex-col bg-white/85 border border-white/60 px-6 py-4 text-[#1a2b4c] backdrop-blur-xl shadow-lg shadow-gray-200/50 transition-all duration-300">
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
                    <button class="flex items-center gap-3 focus:outline-none">
                        <span class="text-sm font-medium text-[#1a2b4c]">{{ Auth::user()->username }}</span>
                    </button>
                    <!-- Simple dropdown for light navbar -->
                    <div class="absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm text-gray-500 whitespace-nowrap overflow-hidden text-ellipsis">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="p-2">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-red-50 rounded-lg transition-colors">
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

        <button id="hamburger-btn" class="md:hidden ml-auto text-[#1a2b4c] focus:outline-none relative z-20 p-2 -mr-2 rounded-lg hover:bg-[#094174]/10 transition">
            <svg class="w-7 h-7 transition-transform duration-300" id="hamburger-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

    </div>

    <div id="mobile-menu" class="w-full md:hidden transition-all duration-500 ease-in-out max-h-0 opacity-0 overflow-hidden">
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
        const btn = document.getElementById('hamburger-btn');
        const menu = document.getElementById('mobile-menu');
        const icon = document.getElementById('hamburger-icon');

        btn.addEventListener('click',() => {
            const isClosed = menu.classList.contains('max-h-0');

            if (isClosed) {
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />';
                menu.classList.remove('max-h-0', 'opacity-0');
                menu.classList.add('max-h-[400px]', 'opacity-100');
            } else {
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />';
                menu.classList.remove('max-h-[400px]', 'opacity-100');
                menu.classList.add('max-h-0', 'opacity-0');
            }
        });
    });
</script>
