<div class="mt-8">
    <div class="bg-gradient-to-br from-[#E6EEF8] to-[#EBF2FA] rounded-3xl p-8 relative overflow-hidden mb-8 shadow-sm">
        <div class="absolute -top-10 -right-10 opacity-5 w-80 h-80 pointer-events-none">
            <svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full text-[#1E40AF]">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm4.59-12.42L10 14.17l-2.59-2.58L6 13l4 4 8-8z"/>
            </svg>
        </div>

        <div class="relative z-10 p-4 sm:p-0">
            <p class="text-[10px] sm:text-[10px] font-bold text-[#6D8A9F] tracking-widest uppercase mb-2">Sumorrow Inventory Core</p>
            <h2 class="text-3xl sm:text-4xl font-normal text-[#094174] tracking-tight">Total Pack Weight:</h2>
            <div class="flex items-baseline gap-2 mt-2">
                <!-- TODO BACKEND: Dinamiskan angka Total Load Item user -->
                <span class="text-6xl sm:text-7xl font-light text-[#5B9DC4] tracking-tighter">12.4</span>
                <span class="text-3xl text-[#6D8A9F] font-light">kg</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-end mt-8 sm:mt-12 gap-4 sm:gap-0">
                <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
                    <!-- TODO BACKEND: Kategorikan load sesuai range berat DB -->
                    <span class="text-[11px] sm:text-xs font-bold text-[#0F172A] tracking-wider">MEDIUM LOAD</span>
                    <span class="text-sm text-[#A0B0C0] font-light hidden sm:inline">|</span>
                    <span class="text-xs sm:text-sm text-[#6D8A9F] mt-1 sm:mt-0 w-full sm:w-auto">Optimized for 3-5 day expeditions</span>
                </div>
                <div class="flex items-center gap-4 sm:gap-6 text-[10px] font-extrabold text-[#6D8A9F] uppercase tracking-wider w-full sm:w-auto justify-between sm:justify-end mt-2 sm:mt-0">
                    <span>Ultralight</span>
                    <span class="text-[#094174]">Limit (20kg)</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[1fr_320px] gap-8">

        <div class="flex flex-col">
            <!-- TODO BACKEND: Untuk filter tab UI bisa diganti ke logic loop category, JS switch, ato form submit param -->
            <div class="bg-[#E5E6FF] rounded-xl p-1.5 flex overflow-x-auto whitespace-nowrap hide-scrollbar mb-6 sm:mb-8 gap-1">
                <button class="px-6 py-2 bg-white rounded-lg text-[13px] font-bold text-[#8C4F34] shadow-sm shrink-0">Essential</button>
                <button class="px-6 py-2 text-[13px] font-medium text-[#6B7280] hover:text-[#0F172A] transition shrink-0">Camping</button>
                <button class="px-6 py-2 text-[13px] font-medium text-[#6B7280] hover:text-[#0F172A] transition shrink-0">Clothing</button>
                <button class="px-6 py-2 text-[13px] font-medium text-[#6B7280] hover:text-[#0F172A] transition shrink-0">Safety</button>
                <button class="px-6 py-2 text-[13px] font-medium text-[#6B7280] hover:text-[#0F172A] transition shrink-0">Misc</button>
            </div>

            <div class="flex justify-between items-center mb-5 px-1">
                <!-- TODO BACKEND: Dinamiskan angkanya -->
                <h3 class="text-base sm:text-lg font-bold text-[#0F172A]">Essential Gear <span class="text-[13px] font-normal text-[#94A3B8] ml-1">(1 items)</span></h3>
                <button class="text-[12px] sm:text-[13px] font-bold text-[#094174] flex items-center gap-1 sm:gap-2 hover:text-blue-600 transition">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add <span class="hidden sm:inline">New Gear</span>
                </button>
            </div>

            <div class="space-y-4">
                <!-- TODO BACKEND: Looping daftar gear per parameter tab yang dibuka -->
                <!-- START: Template Dynamic Gear Item -->
                <div class="bg-white rounded-[20px] p-4 flex flex-col sm:flex-row items-start sm:items-center shadow-[0_2px_15px_-3px_rgba(0,0,0,0.05),0_5px_10px_-2px_rgba(0,0,0,0.02)] gap-4 sm:gap-0">
                    <div class="flex items-center w-full sm:w-auto">
                        <!-- BG Icon diubah ke warna #E5E6FF sesuai yang direquest -->
                        <!-- TODO BACKEND: Emoji dinamis (misal { $item->emoji }}) -->
                        <div class="w-12 h-12 rounded-xl bg-[#E5E6FF] flex items-center justify-center mr-4 text-2xl shrink-0">🎒</div>
                        <div class="flex-1 pr-4 sm:pr-0">
                            <!-- TODO BACKEND: Nama item dinamis -->
                            <h4 class="text-[14px] font-bold text-[#0F172A] leading-tight">Carrier 60L Pro</h4>
                            <!-- TODO BACKEND: Desckripsi item dinamis -->
                            <p class="text-[11px] text-[#94A3B8] mt-0.5 max-w-[200px] truncate sm:max-w-none">Expedition Grade High-Capacity Pack</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between w-full sm:w-auto sm:ml-auto border-t sm:border-t-0 pt-3 sm:pt-0 mt-1 sm:mt-0 border-gray-100 gap-4">
                        <div class="text-left sm:text-right w-1/2 sm:w-auto">
                            <!-- TODO BACKEND: Berat dinamis -->
                            <p class="text-[14px] font-bold text-[#0F172A]">1.8 kg</p>
                            <p class="text-[8px] font-extrabold text-[#94A3B8] uppercase mt-0.5 tracking-wider">Fixed Weight</p>
                        </div>
                        <div class="flex items-center justify-end gap-3 sm:gap-4 text-[#CBD5E1] w-1/2 sm:w-auto">
                            <!-- TODO BACKEND: Action buttons links/methods -->
                            <button class="hover:text-amber-600 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                            <button class="hover:text-red-500 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                            <button class="text-[#2F855A] hover:opacity-80 transition">
                                <svg class="w-5 h-5 drop-shadow-sm" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-9 14l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- END: Template Item Gear -->
                <!-- TODO BACKEND: END LOOP tab pane -->
            </div>

            <div class="mt-6 rounded-[24px] overflow-hidden relative h-48 group shadow-sm">
                <img src="https://images.unsplash.com/photo-1506509536894-386f671bb572?q=80&w=800&fit=crop" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent"></div>
                <div class="absolute bottom-6 left-6 max-w-sm">
                    <h3 class="text-white text-[22px] font-light tracking-wide mb-1">Your Gear, Your Legacy.</h3>
                    <p class="text-white/70 text-[11px] font-light leading-relaxed">Every gram counts when you're reaching for the clouds.<br>Keep your pack lean and your legs strong.</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-6 mt-8 xl:mt-0">

            <div class="bg-[#E5E6FF] rounded-[24px] p-6 shadow-sm">
                <h3 class="font-bold text-[#0F172A] text-[15px] mb-5">Weight Breakdown</h3>
                <ul class="space-y-3.5">
                    <!-- TODO BACKEND: Loop jenis kategori berat (misal: foreach($weightBreakdowns as $breakdown)) -->
                    <!-- START: Template Item Breakdown -->
                    <li class="flex justify-between items-center text-[13px]">
                        <div class="flex items-center gap-2">
                            <!-- TODO BACKEND: Warna dot color dinamis jika perlu -->
                            <span class="w-1.5 h-1.5 rounded-full bg-[#AF4545]"></span>
                            <span class="font-medium text-[#334155]">Essential</span>
                        </div>
                        <span class="font-bold text-[#0F172A]">4.2 kg</span>
                    </li>
                    <!-- END: Template Item Breakdown -->
                    <!-- TODO BACKEND: END LOOP breakdown -->
                </ul>
                <div class="mt-6 pt-4 border-t border-white/60 flex justify-between items-center">
                    <span class="text-[10px] font-bold text-[#6D8A9F] tracking-widest uppercase">Base Weight</span>
                    <span class="font-bold text-[#8C4F34] text-[14px] sm:text-[15px]">11.4 kg</span>
                </div>
            </div>

            <div class="bg-[#0A1F2D] rounded-[24px] p-6 relative overflow-hidden shadow-sm">
                <div class="absolute -right-6 -top-6 opacity-10">
                    <svg viewBox="0 0 24 24" fill="currentColor" class="w-32 h-32 text-white"><path d="M14 6l-4 4 4 4m-8-8l4 4-4 4"/></svg>
                </div>

                <h3 class="text-white text-[16px] sm:text-[17px] font-medium tracking-wide mb-2 relative z-10">Planning for Rinjani?</h3>
                <p class="text-white/60 text-[11px] leading-relaxed mb-5 relative z-10">Based on seasonal forecasts and vertical profile, your pack is missing essential stability gear.</p>

                <div class="space-y-2 relative z-10">
                    <!-- TODO BACKEND: Loop missing safety gear items -->
                    <!-- START: Template Warning Safety Gear -->
                    <div class="bg-white/5 border border-white/10 rounded-xl p-3 flex items-center justify-between hover:bg-white/10 transition cursor-pointer gap-2">
                        <div class="flex items-center gap-2 sm:gap-3 overflow-hidden">
                            <div class="w-6 h-6 rounded-full bg-[#E57B43]/20 flex items-center justify-center text-[#E57B43] text-xs shrink-0">⚠️</div>
                            <div class="truncate pr-1">
                                <p class="text-[11px] sm:text-[12px] font-medium text-white truncate">Carbon Trekking Poles</p>
                                <p class="text-[8px] sm:text-[9px] text-white/50 truncate">Critical for scree slopes</p>
                            </div>
                        </div>
                        <span class="text-white/30 text-xs shrink-0">›</span>
                    </div>
                    <!-- TODO BACKEND: END LOOP missing safety gear -->
                </div>

                <div class="mt-5 relative z-10">
                    <button class="w-full bg-[#FF8E5E] hover:bg-[#E57B43] text-white text-[13px] font-bold py-3 rounded-[12px] transition shadow-md">
                        View Full Safety Checklist
                    </button>
                </div>
            </div>

            <!-- Tip Widget -->
            <div class="bg-[#E9F7F1] rounded-[20px] p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 text-[#4A7C59]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <h3 class="text-[10px] font-extrabold text-[#4A7C59] tracking-widest uppercase">Efficiency Tip</h3>
                </div>
                <p class="text-[12px] text-[#4A7C59]/80 leading-relaxed font-medium">
                    "Try replacing your heavy 1kg boots with 400g trail runners for a 600g immediate pack reduction."
                </p>
            </div>

        </div>
    </div>
</div>
