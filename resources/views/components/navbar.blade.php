<nav
    class="fixed top-6 left-0 right-0 mx-auto w-[95%] max-w-350 rounded-2xl z-50 flex flex-col bg-[#1A1A1A]/80 border border-white/20 px-6 py-4 text-white backdrop-blur-md shadow-lg transition-all duration-300">
    <div class="flex flex-row justify-between items-center w-full relative">
        <div class="hidden md:flex flex-1 text-l font-bold gap-2">
            <a href="/"
                class="px-4 py-2 rounded-lg transition hover:bg-[#094174]/20 hover:text-white text-white">{{ __('common.nav_home') }}</a>
            <a href="/explore"
                class="px-4 py-2 rounded-lg transition hover:bg-[#094174]/20 hover:text-white text-white">{{ __('common.nav_explore') }}</a>
            <a href="/community"
                class="px-4 py-2 rounded-lg transition hover:bg-[#094174]/20 hover:text-white text-white">{{ __('common.nav_community') }}</a>
        </div>

        <div
            class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex justify-center z-10 w-fit pointer-events-none">
            <a href="/" class="pointer-events-auto">
                <img src="{{ asset('images/logo/SUMORROW-LOGO.png') }}" alt="{{ __('common.sumorrow_logo_alt') }}"
                    class="h-10 md:h-12 w-auto transition-transform hover:scale-105" />
            </a>
        </div>

        <div class="hidden md:flex flex-1 justify-end items-center">
            @auth
                <div class="relative group">
                    <button class="flex items-center focus:outline-none transition-transform hover:scale-105">
                        @php
                            $avatar = Auth::user()->avatar_url;
                            $src = null;

                            if ($avatar) {
                                $src = str_contains($avatar, 'http') ? $avatar : asset('storage/' . $avatar);
                            }

                            $defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode(substr(Auth::user()->username, 0, 2)) . '&background=random';
                        @endphp

                        <img src="{{ $avatar ? $src : $defaultAvatar }}" alt="{{ __('common.avatar_alt') }}"
                            class="h-11 w-11 rounded-full object-cover border-2 border-white/40 group-hover:border-white transition-colors shadow-sm">
                    </button>

                    <div
                        class="absolute right-0 mt-3 w-56 bg-[#1A1A1A]/95 border border-white/20 rounded-xl shadow-2xl invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-300 backdrop-blur-lg transform origin-top-right group-hover:scale-100 scale-95 z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-white/10 bg-white/5">
                            <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->username }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="p-2">
                            <a href="{{ route('profile') }}"
                                class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-200 rounded-lg hover:bg-[#094174]/40 hover:text-white transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                {{ __('common.profile') }}
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="mt-1 border-t border-white/10 pt-1 confirm-submit-form" data-confirm-title="{{ __('common.confirm_logout_title') }}" data-confirm-message="{{ __('common.confirm_logout_message') }}" data-confirm-label="{{ __('common.confirm_logout_label') }}" data-confirm-variant="danger">
                                @csrf
                                <button type="submit"
                                    class="flex items-center w-full text-left px-3 py-2.5 text-sm font-medium text-red-400 rounded-lg hover:bg-red-500/15 hover:text-red-300 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    {{ __('common.log_out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('showLogin') }}"
                    class="px-8 py-3 bg-[#094174] text-white font-bold rounded-full transition hover:bg-[#105DA3]">
                    {{ __('common.log_in') }}
                </a>
            @endauth
        </div>

        <button id="hamburger-btn" aria-label="Toggle menu" aria-controls="mobile-menu"
            class="md:hidden ml-auto text-white focus:outline-none relative z-20 p-2 -mr-2 rounded-lg hover:bg-white/10 transition">
            <svg class="h-7 w-7 transition-transform duration-300" id="hamburger-icon" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <div id="mobile-menu"
        class="w-full md:hidden transition-all duration-500 ease-in-out max-h-0 opacity-0 overflow-hidden">
        <div class="flex flex-col gap-4 mt-6 pt-4 border-t border-white/20 text-center pb-2">
            <a href="/" class="font-semibold py-2 rounded-lg hover:bg-white/10 transition">{{ __('common.nav_home') }}</a>
            <a href="/explore" class="font-semibold py-2 rounded-lg hover:bg-white/10 transition">{{ __('common.nav_explore') }}</a>
            <a href="#" class="font-semibold py-2 rounded-lg hover:bg-white/10 transition">{{ __('common.nav_community') }}</a>

            <div class="pt-4 border-t border-white/10">
                @auth
                    <p class="text-sm mb-2 text-gray-400">{{ __('common.logged_in_as', ['username' => Auth::user()->username]) }}</p>
                    <a href="{{ route('profile') }}" class="block py-2 font-semibold text-white">{{ __('common.profile') }}</a>
                    <form action="{{ route('logout') }}" method="POST" class="mt-2 confirm-submit-form" data-confirm-title="{{ __('common.confirm_logout_title') }}" data-confirm-message="{{ __('common.confirm_logout_message') }}" data-confirm-label="{{ __('common.confirm_logout_label') }}" data-confirm-variant="danger">
                        @csrf
                        <button type="submit" class="text-red-400 font-bold">{{ __('common.log_out') }}</button>
                    </form>
                @else
                    <a href="{{ route('showLogin') }}"
                        class="px-8 py-3 bg-[#094174] text-white font-bold rounded-full transition hover:bg-[#105DA3] mx-auto w-fit">{{ __('common.log_in') }}</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
