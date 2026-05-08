<nav
    class="fixed top-6 left-0 right-0 mx-auto w-[95%] max-w-[1400px] rounded-2xl z-50 flex flex-col bg-[#1A1A1A]/18 border border-white/20 px-6 py-4 text-white backdrop-blur-md shadow-lg transition-all duration-300"
>
    <div class="flex flex-row justify-between items-center w-full relative">
        <div class="hidden md:flex flex-1 text-l font-bold gap-2">
            <a
                href="/"
                class="px-4 py-2 rounded-lg transition hover:bg-[#094174]/20 hover:text-white text-white"
                >Home</a
            >
            <a
                href="/explore"
                class="px-4 py-2 rounded-lg transition hover:bg-[#094174]/20 hover:text-white text-white"
                >Explore</a
            >
            <a
                href="#"
                class="px-4 py-2 rounded-lg transition hover:bg-[#094174]/20 hover:text-white text-white"
                >Community</a
            >
        </div>

        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex justify-center z-10 w-fit pointer-events-none">
            <a href="/" class="pointer-events-auto">
                <img
                    src="{{ asset('images/logo/SUMORROW-LOGO.png') }}"
                    alt="Sumorrow Logo"
                    class="h-10 md:h-12 w-auto transition-transform hover:scale-105"
                />
            </a>
        </div>

        <div class="hidden md:flex flex-1 justify-end">
            <a
                href="#"
                class="px-8 py-3 bg-[#094174] text-white font-bold rounded-full transition hover:bg-[#105DA3]"
                >Log in</a
            >
        </div>

        <button id="hamburger-btn" class="md:hidden ml-auto text-white focus:outline-none relative z-20 p-2 -mr-2 rounded-lg hover:bg-white/10 transition">
            <svg class="h-7 w-7 transition-transform duration-300" id="hamburger-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <div id="mobile-menu" class="w-full md:hidden transition-all duration-500 ease-in-out max-h-0 opacity-0 overflow-hidden">
        <div class="flex flex-col gap-4 mt-6 pt-4 border-t border-white/20 text-center pb-2">
            <a href="/" class="font-semibold py-2 rounded-lg hover:bg-white/10 transition">Home</a>
            <a href="/explore" class="font-semibold py-2 rounded-lg hover:bg-white/10 transition">Explore</a>
            <a href="#" class="font-semibold py-2 rounded-lg hover:bg-white/10 transition">Community</a>
            <a href="#" class="mt-2 px-8 py-3 bg-[#094174] text-white font-bold rounded-full transition hover:bg-[#105DA3] mx-auto w-fit">Log in</a>
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
