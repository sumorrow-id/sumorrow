<div class="flex flex-col gap-6">

    {{-- Composer --}}
    <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-4 md:p-5 flex items-center gap-3">
        <img src="{{ asset('images/dummymountain/rinjani.png') }}" class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover shrink-0" alt="Avatar">
        <div class="flex-grow bg-[#F8F9FA] rounded-full flex items-center justify-between px-4 py-2.5 md:py-3">
            <input type="text" placeholder="What is New, Rosita Puspita?" class="bg-transparent border-none p-0 focus:outline-none focus:ring-0 text-sm md:text-base text-gray-800 placeholder-gray-400 w-full font-medium">
            <div class="flex items-center gap-3 text-gray-400 shrink-0">
                <button class="hover:text-[#094174] transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></button>
                <button class="hover:text-[#094174] transition font-bold text-[10px] md:text-xs border-2 border-current rounded-sm px-1 py-0.5" style="line-height: 1;">GIF</button>
                <button class="hover:text-[#094174] transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></button>
            </div>
        </div>
    </div>

    {{-- POST 1 (Gear & Equipment) --}}
    <div class="feed-post bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-5 md:p-6 transition hover:shadow-md duration-300" data-tag="Gear & Equipment">
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/dummymountain/rinjani.png') }}" class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover" alt="Avatar">
                <div>
                    <div class="flex items-center gap-1">
                        <span class="font-bold text-[#1a2b4c] text-sm md:text-base">John Doe</span>
                        <svg class="w-4 h-4 text-blue-500 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm-1.9 14.7L6 12.6l1.5-1.5 2.6 2.6 6.4-6.4 1.5 1.5-7.9 7.9z"/></svg>
                    </div>
                    <div class="text-xs text-gray-500">@realjohndoe</div>
                </div>
            </div>
            <div class="bg-purple-50 text-purple-500 text-[10px] md:text-xs font-bold px-3 py-1 rounded-full whitespace-nowrap">
                Gear
            </div>
        </div>

        <p class="text-[#1a2b4c] text-sm md:text-base mb-2">
            Sepatu trekking yang nyaman tapi ga bikin dompet nangis apa ya?
        </p>
        <div class="text-xs text-gray-400 mb-4 font-medium tracking-wide">
            1:27 PM · April 29 2026
        </div>

        <hr class="border-gray-100 mb-4">

        <div class="flex flex-wrap items-center justify-between text-gray-400 gap-y-3">
            <div class="flex items-center gap-4 sm:gap-6">
                <button onclick="openCommentModal()" class="flex items-center gap-1.5 sm:gap-2 hover:text-[#094174] transition group">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <span class="text-[10px] sm:text-xs md:text-sm font-medium">670</span>
                </button>
                <button onclick="toggleLike(this)" class="flex items-center gap-1.5 sm:gap-2 hover:text-red-500 transition group">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    <span class="text-[10px] sm:text-xs md:text-sm font-medium">6767</span>
                </button>
            </div>
            <div class="flex items-center gap-3 sm:gap-4">
                <button onclick="toggleSave(this)" class="hover:text-[#094174] transition">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                </button>
                <button class="hover:text-[#094174] transition">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- POST 2 (Safety, Tips, Hiking) --}}
    <div class="feed-post bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-5 md:p-6 transition hover:shadow-md duration-300" data-tag="Tips and Trick">
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/dummymountain/batur.jpg') }}" class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover" alt="Avatar">
                <div>
                    <div class="flex items-center gap-1">
                        <span class="font-bold text-[#1a2b4c] text-sm md:text-base">Henry</span>
                    </div>
                    <div class="text-xs text-gray-500">@henryyyyy</div>
                </div>
            </div>
            <div class="flex flex-wrap items-center justify-end gap-2">
                <div class="bg-red-50 text-red-500 text-[10px] md:text-xs font-bold px-3 py-1 rounded-full whitespace-nowrap">Safety and Survival</div>
                <div class="bg-green-50 text-green-500 text-[10px] md:text-xs font-bold px-3 py-1 rounded-full whitespace-nowrap">Tips and Trick</div>
                <div class="bg-[#e5f5fa] text-[#29b6f6] text-[10px] md:text-xs font-bold px-3 py-1 rounded-full whitespace-nowrap hidden sm:block">Hiking Stories</div>
            </div>
        </div>

        <div class="text-[#1a2b4c] text-sm md:text-base mb-2 space-y-4">
            <p>Pas summit attack kemarin, itu pertama kalinya aku ngerasain dingin yang bener-bener 'nembus'... bukan cuma ke badan, tapi ke jiwa juga 🥶</p>
            <p>Padahal udah pakai jaket berlapis, tapi tetep aja menggigil kayak lagi di mode getar. Bonusnya, makin dingin makin cepet capek, makin capek makin kehilangan semangat. Kombinasi kombo yang sangat tidak saya rekomendasikan 👍</p>
            <p>Akhirnya gue dan puncak sepakat untuk tidak bertemu dulu kali ini.</p>
            <p>Sekarang lagi mikir buat nyoba lagi, tapi pengen datang sebagai versi diri yang lebih kuat (dan lebih hangat).</p>
            <p>Ada tips ga biar summit attack berikutnya ga jadi ajang survival doang? Terutama soal fisik sama perlengkapan 🙏</p>
        </div>
        <div class="text-xs text-gray-400 mb-4 font-medium tracking-wide">
            1:27 PM · April 29 2026
        </div>

        <hr class="border-gray-100 mb-4">

        <div class="flex items-center justify-between text-gray-400">
            <div class="flex items-center gap-6">
                <button onclick="openCommentModal()" class="flex items-center gap-2 hover:text-[#094174] transition group">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <span class="text-xs md:text-sm font-medium">1.2K</span>
                </button>
                <button onclick="toggleLike(this)" class="flex items-center gap-2 hover:text-red-500 transition group text-red-500">
                    <svg class="w-5 h-5 fill-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    <span class="text-xs md:text-sm font-medium">80K</span>
                </button>
            </div>
            <div class="flex items-center gap-4">
                <button onclick="toggleSave(this)" class="hover:text-[#094174] transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                </button>
                <button class="hover:text-[#094174] transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- POST 3 (Hiking Stories) --}}
    <div class="feed-post bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-5 md:p-6 transition hover:shadow-md duration-300" data-tag="Hiking Stories">
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/dummymountain/sindoro.jpg') }}" class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover" alt="Avatar">
                <div>
                    <div class="flex items-center gap-1">
                        <span class="font-bold text-[#1a2b4c] text-sm md:text-base">Kirei</span>
                    </div>
                    <div class="text-xs text-gray-500">@kireikanan</div>
                </div>
            </div>
            <div class="bg-[#e5f5fa] text-[#29b6f6] text-[10px] md:text-xs font-bold px-3 py-1 rounded-full whitespace-nowrap">
                Hiking Stories
            </div>
        </div>

        <div class="text-[#1a2b4c] text-sm md:text-base mb-3">
            <p>Ekspektasi: menikmati sunrise 🌅</p>
            <p>Realita: menggigil sambil nanya 'ini masih lama?'</p>
        </div>

        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=1200&auto=format&fit=crop" class="w-full h-48 md:h-64 object-cover rounded-xl mb-3" alt="Sunrise Mountain">

        <div class="text-xs text-gray-400 mb-4 font-medium tracking-wide">
            1:27 PM · April 29 2026
        </div>

        <hr class="border-gray-100 mb-4">

        <div class="flex items-center justify-between text-gray-400">
            <div class="flex items-center gap-6">
                <button onclick="openCommentModal()" class="flex items-center gap-2 hover:text-[#094174] transition group">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <span class="text-xs md:text-sm font-medium">2.6K</span>
                </button>
                <button onclick="toggleLike(this)" class="flex items-center gap-2 hover:text-red-500 transition group">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    <span class="text-xs md:text-sm font-medium">10.1K</span>
                </button>
            </div>
            <div class="flex items-center gap-4">
                <button onclick="toggleSave(this)" class="hover:text-[#094174] transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                </button>
                <button class="hover:text-[#094174] transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- POST 4 (Rinjani Story) --}}
    <div class="feed-post bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-5 md:p-6 transition hover:shadow-md duration-300" data-tag="Hiking Stories">
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/dummymountain/rinjani.png') }}" class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover" alt="Avatar">
                <div>
                    <div class="flex items-center gap-1">
                        <span class="font-bold text-[#1a2b4c] text-sm md:text-base">Mbak Mawar</span>
                    </div>
                    <div class="text-xs text-gray-500">@mawar_merah</div>
                </div>
            </div>
            <div class="bg-[#e5f5fa] text-[#29b6f6] text-[10px] md:text-xs font-bold px-3 py-1 rounded-full whitespace-nowrap">
                Hiking Stories
            </div>
        </div>

        <div class="text-[#1a2b4c] text-sm md:text-base mb-2 space-y-4">
            <p><strong>Catatan Perjalanan Rinjani (3726 mdpl): Sebuah Ujian Mental & Fisik</strong></p>
            <p>Berangkat dari Sembalun jam 8 pagi, cuaca terik luar biasa. Padang savana yang indah dari jauh nyatanya sukses bikin kulit gosong dan stamina terkuras abis sebelum masuk Pos 2. Pas nyampe Plawangan Sembalun sore harinya—wah, capeknya langsung ilang. Liat Segara Anak dari atas plus sunset bener-bener kayak mimpi!</p>
            <p>Tapi drama baru dimulai pas <em>summit attack</em> jam 1 pagi. Track berpasir dan kerikil! Maju dua langkah, merosot selangkah. Anginnya jangan ditanya, dinginnya nembus tulang walau udah pakai jaket berlapis. Berkali-kali mau nyerah pas ngeliat puncak rasanya nggak deket-deket.</p>
            <p>Begitu sampai di puncak jam 6 pagi pas matahari terbit, air mata rasanya mau netes. View seluruh Lombok, siluet Gunung Agung di Bali, dan Segara Anak di bawah sana... <em>it was absolute perfection</em>. Benar kata orang, Rinjani itu butuh fisik yang kuat, tapi yang nganterin kamu sampai ke puncak sebenarnya adalah mental! 🏔️✨</p>
        </div>

        <img src="{{ asset('images/dummymountain/batur.jpg') }}" class="w-full h-48 md:h-64 object-cover rounded-xl mb-3" alt="Rinjani Summit">

        <div class="text-xs text-gray-400 mb-4 font-medium tracking-wide">
            8:15 AM · Mei 10 2026
        </div>

        <hr class="border-gray-100 mb-4">

        <div class="flex items-center justify-between text-gray-400">
            <div class="flex items-center gap-6">
                <button onclick="openCommentModal()" class="flex items-center gap-2 hover:text-[#094174] transition group">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <span class="text-xs md:text-sm font-medium">324</span>
                </button>
                <button onclick="toggleLike(this)" class="flex items-center gap-2 hover:text-red-500 transition group">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    <span class="text-xs md:text-sm font-medium">12.4K</span>
                </button>
            </div>
            <div class="flex items-center gap-4">
                <button onclick="toggleSave(this)" class="hover:text-[#094174] transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                </button>
                <button class="hover:text-[#094174] transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                </button>
            </div>
        </div>
    </div>

</div>
