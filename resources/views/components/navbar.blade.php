<nav
    class="fixed top-6 left-0 right-0 mx-auto w-[95%] rounded-2xl z-50 flex justify-between items-center bg-[#1A1A1A]/18 border border-white/20 px-6 py-4 text-white backdrop-blur-md shadow-lg"
>
    <div class="flex text-l font-bold gap-2">
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
    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2">
        <a href="/">
            <img
                src="{{ asset('images/logo/SUMORROW-LOGO.png') }}"
                alt="Sumorrow Logo"
                class="h-12 w-auto"
            />
        </a>
    </div>
    <div class="flex items-center">
        @auth
            <div class="relative group">
                <button class="flex items-center gap-3 focus:outline-none">
                    <span class="hidden md:block text-sm font-medium text-white">{{ Auth::user()->username }}</span>
                    
                    @php
                        $avatar = Auth::user()->avatar_url;
                        // Jika mengandung 'http', berarti itu foto dari Google Socialite
                        $src = str_contains($avatar, 'http') ? $avatar : asset('storage/' . $avatar);
                        
                        // Warna Navy Sumorrow: 094174
                        $defaultAvatar = "https://ui-avatars.com/api/?name=" . urlencode(Auth::user()->username) . "&background=094174&color=fff&bold=true";
                    @endphp

                    @if($avatar)
                        <img src="{{ $src }}" 
                            alt="Profile" class="h-10 w-10 rounded-full object-cover border border-white/40">
                    @else
                        <img src="{{ $defaultAvatar }}" 
                            alt="Profile" class="h-10 w-10 rounded-full border border-white/40">
                    @endif
                </button>

                <!-- Dropdown Sederhana (Muncul saat hover group) -->
                <div class="absolute right-0 mt-2 w-48 bg-[#1A1A1A] border border-white/20 rounded-xl shadow-xl py-2 invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-300">
                    <a href="{{ route('home') }}" class="block px-4 py-2 text-sm text-white hover:bg-[#094174]/20">Profile</a>
                    <hr class="border-white/10 my-1">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-red-500/10">
                            Log out
                        </button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}" class="px-8 py-3 bg-[#094174] text-white font-bold rounded-full transition hover:bg-[#105DA3]">
                Log in
            </a>
        @endauth
    </div>
</nav>
