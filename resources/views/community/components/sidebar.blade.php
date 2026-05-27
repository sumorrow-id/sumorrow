<div class="flex flex-col gap-6">


    {{-- Search Bar --}}
    <div class="relative w-full">
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <input type="text" class="block w-full pl-11 pr-4 py-3 bg-[#F8F9FA] rounded-xl text-sm md:text-base border-none focus:ring-0 focus:outline-none placeholder-gray-400" placeholder="Search...">
    </div>

    {{-- 1. Forum Leaders --}}
    <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-5 md:p-6">
        <h2 class="font-bold text-lg text-[#1a2b4c] mb-2 flex items-center justify-between cursor-pointer group">
            <span>Forum Leaders</span>
            <svg class="w-4 h-4 text-[#1a2b4c] group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </h2>
        <p class="text-xs md:text-sm text-gray-500 mb-5 leading-relaxed">Post, reply, and support others to climb the Forum Leaders board.</p>

        <div class="flex flex-col gap-5">
            {{-- [BACKEND] Loop through Top 3 Contributors --}}
            <div class="flex items-center gap-4">
                <div class="relative shrink-0">
                    <img src="{{ asset('images/dummymountain/rinjani.png') }}" alt="User" class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover">
                </div>
                <div>
                    <div class="font-bold text-[#1a2b4c] text-sm md:text-base">John Doe</div>
                    <div class="text-[10px] md:text-xs text-gray-500 uppercase tracking-wide">1.2K CONTRIBUTIONS</div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative shrink-0">
                    <img src="{{ asset('images/dummymountain/batur.jpg') }}" alt="User" class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover">
                </div>
                <div>
                    <div class="font-bold text-[#1a2b4c] text-sm md:text-base">Henry</div>
                    <div class="text-[10px] md:text-xs text-gray-500 uppercase tracking-wide">1K CONTRIBUTIONS</div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative shrink-0">
                    <img src="{{ asset('images/dummymountain/sindoro.jpg') }}" alt="User" class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover">
                </div>
                <div>
                    <div class="font-bold text-[#1a2b4c] text-sm md:text-base">Yuri</div>
                    <div class="text-[10px] md:text-xs text-gray-500 uppercase tracking-wide">998 CONTRIBUTIONS</div>
                </div>
            </div>
            {{-- [BACKEND] End of Contributors Loop --}}
        </div>
    </div>

    {{-- 2. My Community --}}
    <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-5 md:p-6">
        <h2 onclick="switchTab('community')" class="font-bold text-lg text-[#1a2b4c] mb-5 flex items-center justify-between cursor-pointer group hover:text-[#094174] transition">
            <span>My Community</span>
            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </h2>

        <div class="flex flex-col gap-5">
            {{-- [BACKEND] Loop through Top 3 User's Communities --}}
            <div class="flex items-center gap-4 cursor-pointer group pb-1">
                <img src="{{ asset('images/dummymountain/bromo.jpg') }}" alt="Community" class="w-12 h-12 md:w-14 md:h-14 rounded-xl object-cover shrink-0">
                <div class="flex flex-col justify-center">
                    <span class="text-[9px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">PUBLIC</span>
                    <span class="font-bold text-[#1a2b4c] text-sm md:text-base group-hover:text-[#094174] transition">Summit Space</span>
                    <span class="text-xs text-gray-400 mt-0.5">12.2k Members</span>
                </div>
            </div>

            <div class="flex items-center gap-4 cursor-pointer group pb-1">
                <img src="{{ asset('images/dummymountain/merbabu.png') }}" alt="Community" class="w-12 h-12 md:w-14 md:h-14 rounded-xl object-cover shrink-0">
                <div class="flex flex-col justify-center">
                    <span class="text-[9px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">PUBLIC</span>
                    <span class="font-bold text-[#1a2b4c] text-sm md:text-base group-hover:text-[#094174] transition">Pendaki Rebahan</span>
                    <span class="text-xs text-gray-400 mt-0.5">67.1k Members</span>
                </div>
            </div>

            <div class="flex items-center gap-4 cursor-pointer group pb-1">
                <img src="{{ asset('images/dummymountain/rinjani.png') }}" alt="Community" class="w-12 h-12 md:w-14 md:h-14 rounded-xl object-cover shrink-0">
                <div class="flex flex-col justify-center">
                    <span class="text-[9px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">PRIVATE</span>
                    <span class="font-bold text-[#1a2b4c] text-sm md:text-base group-hover:text-[#094174] transition leading-tight">HIMIL SQUAD OTW TO PRAU</span>
                    <span class="text-xs text-gray-400 mt-0.5">5 Members</span>
                </div>
            </div>
            {{-- [BACKEND] End of Communities Loop --}}
        </div>
    </div>

    {{-- 3. Popular Tags --}}
    <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-5 md:p-6">
        <h2 class="font-bold text-lg text-[#1a2b4c] mb-5">
            Popular Tags
        </h2>

        <div class="flex flex-col gap-5">
            {{-- [BACKEND] Loop through Top 4 Popular Tags --}}
            <div class="tag-filter-btn flex items-center gap-4 cursor-pointer group p-2 rounded-xl transition" onclick="filterByTag(this, 'Hiking Stories')">
                <div class="w-12 h-12 rounded-xl bg-[#e5f5fa] text-[#29b6f6] shrink-0 flex items-center justify-center group-hover:scale-105 transition">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12.92,10.68L12.5,13.5l1.62-1.62l0-1.88L12.92,10.68z M14,13.62L12.55,15h-0.2L12,13.23l-0.78-4.2 l-1.84,1.84L9,11.23l1.84-2.84L10,4H8L8,2h4v5.3l0.39,2.1C13.28,10.29,14,11.39,14,12.6V13.62z M8,5v4.25L7,11l-3.32-2.31 L2.57,9.5L6.5,12.5h-1l-3,3l1.41,1.41l2.5-2.5V22h2L8.41,14.41L8,5z"/></svg>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-[#1a2b4c] text-sm md:text-base group-hover:text-[#29b6f6] transition">Hiking Stories</span>
                    <span class="text-xs text-gray-400">98,323 posted by this tag</span>
                </div>
            </div>

            <div class="tag-filter-btn flex items-center gap-4 cursor-pointer group p-2 rounded-xl transition" onclick="filterByTag(this, 'Tips and Trick')">
                <div class="w-12 h-12 rounded-xl bg-green-50 text-green-500 shrink-0 flex items-center justify-center group-hover:scale-105 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-[#1a2b4c] text-sm md:text-base group-hover:text-green-500 transition">Tips and Trick</span>
                    <span class="text-xs text-gray-400">98,323 posted by this tag</span>
                </div>
            </div>

            <div class="tag-filter-btn flex items-center gap-4 cursor-pointer group p-2 rounded-xl transition" onclick="filterByTag(this, 'Gear & Equipment')">
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-500 shrink-0 flex items-center justify-center group-hover:scale-105 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-[#1a2b4c] text-sm md:text-base group-hover:text-purple-500 transition">Gear & Equipment</span>
                    <span class="text-xs text-gray-400">98,323 posted by this tag</span>
                </div>
            </div>

            <div class="tag-filter-btn flex items-center gap-4 cursor-pointer group p-2 rounded-xl transition" onclick="filterByTag(this, 'Safety & Survival')">
                <div class="w-12 h-12 rounded-xl bg-red-50 text-red-500 shrink-0 flex items-center justify-center group-hover:scale-105 transition">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8l-4 4h3v4h2v-4h3l-4-4zM2 13h2C4 8.58 7.58 5 12 5s8 3.58 8 8h2c0-5.52-4.48-10-10-10S2 7.48 2 13z"></path></svg>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-[#1a2b4c] text-sm md:text-base group-hover:text-red-500 transition">Safety & Survival</span>
                    <span class="text-xs text-gray-400">98,323 posted by this tag</span>
                </div>
            </div>
            {{-- [BACKEND] End of Tags Loop --}}
        </div>
    </div>

    {{-- 4. Who to Follow --}}
    <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-5 md:p-6 mb-8">
        <h2 class="font-bold text-lg text-[#1a2b4c] mb-5 flex items-center justify-between cursor-pointer group hover:text-[#094174] transition">
            <span>Who to Follow</span>
            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </h2>

        <div class="flex flex-col gap-5">
            {{-- [BACKEND] Loop through top recommended users --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/dummymountain/rinjani.png') }}" alt="John" class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover">
                    <div>
                        <div class="flex items-center gap-1">
                            <span class="font-bold text-[#1a2b4c] text-sm md:text-base">John Doe</span>
                            <svg class="w-4 h-4 text-blue-500 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm-1.9 14.7L6 12.6l1.5-1.5 2.6 2.6 6.4-6.4 1.5 1.5-7.9 7.9z"/></svg>
                        </div>
                        <div class="text-xs text-gray-500">@realjohndoe</div>
                    </div>
                </div>
                <button onclick="toggleFollow(this)" class="bg-[#094174] border border-[#094174] text-white text-xs md:text-sm font-bold px-4 py-1.5 md:px-5 md:py-2 rounded-full transition-all duration-300 transform active:scale-95">Follow</button>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/dummymountain/batur.jpg') }}" alt="Henry" class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover">
                    <div>
                        <div class="flex items-center gap-1">
                            <span class="font-bold text-[#1a2b4c] text-sm md:text-base">Henry</span>
                        </div>
                        <div class="text-xs text-gray-500">@henryyyyy</div>
                    </div>
                </div>
                <button onclick="toggleFollow(this)" class="bg-[#094174] border border-[#094174] text-white text-xs md:text-sm font-bold px-4 py-1.5 md:px-5 md:py-2 rounded-full transition-all duration-300 transform active:scale-95">Follow</button>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/dummymountain/sindoro.jpg') }}" alt="Kirei" class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover">
                    <div>
                        <div class="flex items-center gap-1">
                            <span class="font-bold text-[#1a2b4c] text-sm md:text-base">Kirei</span>
                        </div>
                        <div class="text-xs text-gray-500">@kireikanan</div>
                    </div>
                </div>
                <button onclick="toggleFollow(this)" class="bg-[#094174] border border-[#094174] text-white text-xs md:text-sm font-bold px-4 py-1.5 md:px-5 md:py-2 rounded-full transition-all duration-300 transform active:scale-95">Follow</button>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/dummymountain/merbabu.png') }}" alt="Yuri" class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover">
                    <div>
                        <div class="flex items-center gap-1">
                            <span class="font-bold text-[#1a2b4c] text-sm md:text-base">Yuri</span>
                        </div>
                        <div class="text-xs text-gray-500">@iyupp</div>
                    </div>
                </div>
                <button onclick="toggleFollow(this)" class="bg-[#094174] border border-[#094174] text-white text-xs md:text-sm font-bold px-4 py-1.5 md:px-5 md:py-2 rounded-full transition-all duration-300 transform active:scale-95">Follow</button>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/dummymountain/bromo.jpg') }}" alt="Leon" class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover">
                    <div>
                        <div class="flex items-center gap-1">
                            <span class="font-bold text-[#1a2b4c] text-sm md:text-base">Leon</span>
                        </div>
                        <div class="text-xs text-gray-500">@pecellele</div>
                    </div>
                </div>
                <button onclick="toggleFollow(this)" class="bg-[#094174] border border-[#094174] text-white text-xs md:text-sm font-bold px-4 py-1.5 md:px-5 md:py-2 rounded-full transition-all duration-300 transform active:scale-95">Follow</button>
            </div>
            {{-- [BACKEND] End of Follow Recommendation Loop --}}
        </div>
    </div>

</div>
