@php
    $totalWeight = $gears->sum('weight_grams') / 1000;
    
    // Group gears by category
    $gearsByCategory = $gears->groupBy('category');
    $categories = $gears->pluck('category')->unique();

    // Weight Category logic
    if ($totalWeight == 0) {
        $loadType = 'EMPTY LOAD';
        $loadDesc = 'Add gear to see your pack weight';
        $maxWeight = 0;
    } elseif ($totalWeight < 10) {
        $loadType = 'ULTRALIGHT LOAD';
        $loadDesc = 'Optimized for speed and agility';
        $maxWeight = 15;
    } elseif ($totalWeight <= 20) {
        $loadType = 'MEDIUM LOAD';
        $loadDesc = 'Optimized for 3-5 day expeditions';
        $maxWeight = 20;
    } else {
        $loadType = 'HEAVY LOAD';
        $loadDesc = 'Heavy duty expedition';
        $maxWeight = 35;
    }

    // Colors mapping for breakdown
    $categoryColors = [
        'Backpack' => '#AF4545',
        'Tent' => '#457BAF',
        'Apparel' => '#45AF64',
        'Footwear' => '#AF8A45',
        'Cooking' => '#AF458E',
        'Accessories' => '#8B5CF6',
        'Other' => '#64748B',
        'default' => '#7A8C9F'
    ];

    // Icons mapping
    $categoryIcons = [
        'Backpack' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#AF4545]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2Z"/><path d="M8 10h8"/><path d="M8 18h8"/><path d="M8 22v-6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v6"/><path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/></svg>',
        'Tent' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#457BAF]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3.5 21 14 3"/><path d="M20.5 21 10 3"/><path d="M15.5 21 12 15l-3.5 6"/><path d="M2 21h20"/></svg>',
        'Apparel' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#45AF64]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"/></svg>',
        'Footwear' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#AF8A45]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13.43A4 4 0 0 0 14.57 12H13a2 2 0 0 0-2 2v1h-.5a1.5 1.5 0 0 1-1.5-1.5V11a2 2 0 0 1 2-2h2a2 2 0 0 0 2-2V5"/><path d="M22 20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-5l2-1.5V10a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v3s2.5 1.5 4 1.5h1.5a3 3 0 0 0 2.82-2H22l.06 6.13Z"/></svg>',
        'Cooking' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#AF458E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>',
        'Accessories' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#8B5CF6]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>',
        'Other' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#64748B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/></svg>',
        'default' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#7A8C9F]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>'
    ];
@endphp
<div class="mt-8">
    <div class="bg-linear-to-br from-[#E6EEF8] to-[#EBF2FA] rounded-3xl p-8 relative overflow-hidden mb-8 shadow-sm">
        <div class="absolute -top-10 -right-10 opacity-5 w-80 h-80 pointer-events-none">
            <svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full text-[#1E40AF]">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm4.59-12.42L10 14.17l-2.59-2.58L6 13l4 4 8-8z"/>
            </svg>
        </div>

        <div class="relative z-10 p-4 sm:p-0">
            <p class="text-[10px] sm:text-[10px] font-bold text-[#6D8A9F] tracking-widest uppercase mb-2">Sumorrow Inventory Core</p>
            <h2 class="text-3xl sm:text-4xl font-normal text-[#094174] tracking-tight">Total Pack Weight:</h2>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-6xl sm:text-7xl font-light text-[#5B9DC4] tracking-tighter">{{ number_format($totalWeight, 1) }}</span>
                <span class="text-3xl text-[#6D8A9F] font-light">kg</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-end mt-8 sm:mt-12 gap-4 sm:gap-0">
                <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
                    <span class="text-[11px] sm:text-xs font-bold text-[#0F172A] tracking-wider">{{ $loadType }}</span>
                    <span class="text-sm text-[#A0B0C0] font-light hidden sm:inline">|</span>
                    <span class="text-xs sm:text-sm text-[#6D8A9F] mt-1 sm:mt-0 w-full sm:w-auto">{{ $loadDesc }}</span>
                </div>
                <div class="flex items-center gap-4 sm:gap-6 text-[10px] font-extrabold text-[#6D8A9F] uppercase tracking-wider w-full sm:w-auto justify-between sm:justify-end mt-2 sm:mt-0">
                    <span>Limit ({{ $maxWeight }}kg)</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[1fr_320px] gap-8">

        <div class="flex flex-col">
            @if($categories->isNotEmpty())
            <div class="bg-[#E5E6FF] rounded-xl p-1.5 flex overflow-x-auto whitespace-nowrap hide-scrollbar mb-6 sm:mb-8 gap-1">
                <button onclick="filterGear('all')" id="gear-btn-all" 
                    class="gear-cat-btn px-6 py-2 bg-white font-bold text-[#8C4F34] shadow-sm rounded-lg text-[13px] transition shrink-0">
                    All Gear
                </button>
                @foreach($categories as $index => $cat)
                    <button onclick="filterGear('{{ Str::slug($cat) }}')" id="gear-btn-{{ Str::slug($cat) }}" 
                        class="gear-cat-btn px-6 py-2 font-medium text-[#6B7280] hover:text-[#0F172A] rounded-lg text-[13px] transition shrink-0">
                        {{ $cat }}
                    </button>
                @endforeach
            </div>
            @endif

            <div class="flex justify-between items-center mb-5 px-1">
                <h3 class="text-base sm:text-lg font-bold text-[#0F172A]" id="gear-category-title">All Gear <span class="text-[13px] font-normal text-[#94A3B8] ml-1" id="gear-category-count">({{ $gears->count() }} items)</span></h3>
                <button onclick="openGearModal()" class="text-[12px] sm:text-[13px] font-bold text-[#094174] flex items-center gap-1 sm:gap-2 hover:text-blue-600 transition">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add <span class="hidden sm:inline">New Gear</span>
                </button>
            </div>

            <div class="space-y-4">
                @forelse($gears as $item)
                    <div class="gear-item bg-white rounded-[20px] p-4 flex flex-col sm:flex-row items-start sm:items-center shadow-[0_2px_15px_-3px_rgba(0,0,0,0.05),0_5px_10px_-2px_rgba(0,0,0,0.02)] gap-4 sm:gap-0 transition-opacity" data-category="{{ Str::slug($item->category) }}">
                        <div class="flex items-center w-full sm:w-auto">
                            <div class="w-12 h-12 rounded-xl bg-[#F1F5F9] flex items-center justify-center mr-4 shrink-0 shadow-inner">
                                {!! $categoryIcons[$item->category] ?? $categoryIcons['default'] !!}
                            </div>
                            <div class="flex-1 pr-4 sm:pr-0">
                                <h4 class="text-[14px] font-bold text-[#0F172A] leading-tight">{{ $item->name }}</h4>
                                <p class="text-[11px] text-[#94A3B8] mt-0.5 max-w-50 truncate sm:max-w-none">{{ $item->brand ?? 'No Brand' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between w-full sm:w-auto sm:ml-auto border-t sm:border-t-0 pt-3 sm:pt-0 mt-1 sm:mt-0 border-gray-100 gap-4">
                            <div class="text-left sm:text-right w-1/2 sm:w-auto">
                                <p class="text-[14px] font-bold text-[#0F172A]">{{ number_format($item->weight_grams / 1000, 2) }} kg</p>
                                <p class="text-[8px] font-extrabold text-[#94A3B8] uppercase mt-0.5 tracking-wider">Weight</p>
                            </div>
                            <div class="flex items-center justify-end gap-3 sm:gap-4 text-[#CBD5E1] w-1/2 sm:w-auto">
                                <button onclick="editGear({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ addslashes($item->brand ?? '') }}', {{ $item->weight_grams }}, '{{ addslashes($item->category) }}')" class="hover:text-amber-600 transition" title="Edit Gear"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                                <form action="{{ route('gears.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this gear?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="hover:text-red-500 transition" title="Delete Gear"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-[20px] p-10 text-center shadow-sm flex flex-col items-center justify-center border border-gray-100">
                        <div class="w-16 h-16 bg-[#F8FAFC] rounded-2xl flex items-center justify-center mb-4 text-[#94A3B8] shadow-inner border border-gray-100">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <h4 class="text-[16px] font-bold text-[#0F172A] mb-2">Your pack is empty</h4>
                        <p class="text-[#64748B] text-[13px] max-w-sm mx-auto leading-relaxed">Start building your inventory to track weight, analyze your loadout, and get smart recommendations for your next hike.</p>
                        <button onclick="openGearModal()" class="mt-6 px-6 py-2.5 bg-[#094174] hover:bg-[#1E40AF] text-white text-[13px] font-bold rounded-xl transition shadow-md flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Your First Gear
                        </button>
                    </div>
                @endforelse
            </div>

            <div class="mt-6 rounded-3xl overflow-hidden relative h-48 group shadow-sm">
                <img src="{{ asset('images/profile/gear.jpeg') }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-linear-to-r from-black/80 via-black/40 to-transparent"></div>
                <div class="absolute bottom-6 left-6 max-w-sm">
                    <h3 class="text-white text-[22px] font-light tracking-wide mb-1">Your Gear, Your Legacy.</h3>
                    <p class="text-white/70 text-[11px] font-light leading-relaxed">Every gram counts when you're reaching for the clouds.<br>Keep your pack lean and your legs strong.</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-6 mt-8 xl:mt-0">
            <div class="bg-[#E5E6FF] rounded-3xl p-6 shadow-sm">
                <h3 class="font-bold text-[#0F172A] text-[15px] mb-5">Weight Breakdown</h3>
                <ul class="space-y-3.5">
                    @foreach($gearsByCategory as $cat => $catGears)
                        <li class="flex justify-between items-center text-[13px]">
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $categoryColors[$cat] ?? $categoryColors['default'] }};"></span>
                                <span class="font-medium text-[#334155]">{{ $cat }}</span>
                            </div>
                            <span class="font-bold text-[#0F172A]">{{ number_format($catGears->sum('weight_grams') / 1000, 2) }} kg</span>
                        </li>
                    @endforeach
                    @if($gearsByCategory->isEmpty())
                        <li class="text-[#64748B] text-[13px] text-center italic py-4">Add gear to see breakdown</li>
                    @endif
                </ul>
                <div class="mt-6 pt-4 border-t border-white/60 flex justify-between items-center">
                    <span class="text-[10px] font-bold text-[#6D8A9F] tracking-widest uppercase">Base Weight</span>
                    <span class="font-bold text-[#8C4F34] text-[14px] sm:text-[15px]">{{ number_format($totalWeight, 1) }} kg</span>
                </div>
            </div>
            
            @if($totalWeight > 25)
                <!-- Tip Widget -->
                <div class="bg-[#E9F7F1] rounded-[20px] p-5 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-[#4A7C59]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <h3 class="text-[10px] font-extrabold text-[#4A7C59] tracking-widest uppercase">Efficiency Tip</h3>
                    </div>
                    <p class="text-[12px] text-[#4A7C59]/80 leading-relaxed font-medium">
                        "Your pack is quite heavy. Consider replacing your heaviest items or leaving non-essentials to prevent fatigue."
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Gear Modal -->
<div id="gear-modal" class="fixed inset-0 z-100 flex items-center justify-center hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-[#0F172A]/40 backdrop-blur-sm" onclick="closeGearModal()"></div>
    
    <!-- Modal Content -->
    <div class="relative bg-white rounded-3xl w-full max-w-md p-6 sm:p-8 m-4 shadow-2xl transform transition-all">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-[#0F172A]" id="gear-modal-title">Add New Gear</h3>
            <button onclick="closeGearModal()" class="text-[#94A3B8] hover:text-[#0F172A] transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 text-red-500 text-sm rounded-xl">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    document.getElementById('gear-modal').classList.remove('hidden');
                });
            </script>
        @endif

        <form id="gear-form" method="POST" action="{{ route('gears.store') }}">
            @csrf
            <input type="hidden" name="_method" id="gear-method" value="POST">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-[13px] font-bold text-[#334155] mb-1">Gear Name</label>
                    <input type="text" name="name" id="gear-name" required value="{{ old('name') }}"
                        class="w-full bg-[#F8FAFC] border-none rounded-xl px-4 py-3 text-sm text-[#0F172A] focus:ring-2 focus:ring-[#094174]/20 transition"
                        placeholder="e.g. Osprey Atmos AG 65">
                </div>
                
                <div>
                    <label class="block text-[13px] font-bold text-[#334155] mb-1">Brand (Optional)</label>
                    <input type="text" name="brand" id="gear-brand" value="{{ old('brand') }}"
                        class="w-full bg-[#F8FAFC] border-none rounded-xl px-4 py-3 text-sm text-[#0F172A] focus:ring-2 focus:ring-[#094174]/20 transition"
                        placeholder="e.g. Osprey">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[13px] font-bold text-[#334155] mb-1">Weight (grams)</label>
                        <input type="number" name="weight_grams" id="gear-weight" required min="0" value="{{ old('weight_grams') }}"
                            class="w-full bg-[#F8FAFC] border-none rounded-xl px-4 py-3 text-sm text-[#0F172A] focus:ring-2 focus:ring-[#094174]/20 transition"
                            placeholder="e.g. 2100">
                    </div>
                    <div>
                        <label class="block text-[13px] font-bold text-[#334155] mb-1">Category</label>
                        <select name="category" id="gear-category" required
                            class="w-full bg-[#F8FAFC] border-none rounded-xl px-4 py-3 text-sm text-[#0F172A] focus:ring-2 focus:ring-[#094174]/20 transition appearance-none">
                            <option value="Backpack">Backpack</option>
                            <option value="Tent">Tent</option>
                            <option value="Apparel">Apparel</option>
                            <option value="Footwear">Footwear</option>
                            <option value="Cooking">Cooking</option>
                            <option value="Accessories">Accessories</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex gap-3">
                <button type="button" onclick="closeGearModal()" 
                    class="flex-1 px-4 py-3 bg-[#F1F5F9] hover:bg-[#E2E8F0] text-[#475569] font-bold rounded-xl text-sm transition">
                    Cancel
                </button>
                <button type="submit" 
                    class="flex-1 px-4 py-3 bg-[#094174] hover:bg-[#1E40AF] text-white font-bold rounded-xl text-sm transition shadow-md">
                    Save Gear
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function filterGear(categorySlug) {
        // Reset all buttons
        document.querySelectorAll('.gear-cat-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'text-[#8C4F34]', 'font-bold', 'shadow-sm');
            btn.classList.add('text-[#6B7280]', 'font-medium');
        });

        // Set active button
        let activeBtn = document.getElementById('gear-btn-' + categorySlug);
        if(activeBtn) {
            activeBtn.classList.add('bg-white', 'text-[#8C4F34]', 'font-bold', 'shadow-sm');
            activeBtn.classList.remove('text-[#6B7280]', 'font-medium');
        }

        // Filter items
        let count = 0;
        document.querySelectorAll('.gear-item').forEach(item => {
            if (categorySlug === 'all' || item.dataset.category === categorySlug) {
                item.style.display = 'flex';
                count++;
            } else {
                item.style.display = 'none';
            }
        });

        // Update Title & Count
        let catBtn = document.querySelector('#gear-btn-' + categorySlug);
        let catName = catBtn ? catBtn.innerText.trim() : 'All Gear';
        document.getElementById('gear-category-title').innerHTML = `${catName} <span class="text-[13px] font-normal text-[#94A3B8] ml-1" id="gear-category-count">(${count} items)</span>`;
    }

    // Trigger first category filter on load if available
    document.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('gear-btn-all')) {
            filterGear('all');
        }
    });

    function openGearModal() {
        document.getElementById('gear-modal').classList.remove('hidden');
        document.getElementById('gear-form').action = "{{ route('gears.store') }}";
        document.getElementById('gear-method').value = "POST";
        document.getElementById('gear-modal-title').innerText = "Add New Gear";
        document.getElementById('gear-name').value = "";
        document.getElementById('gear-brand').value = "";
        document.getElementById('gear-weight').value = "";
        document.getElementById('gear-category').value = "Backpack";
    }

    function closeGearModal() {
        document.getElementById('gear-modal').classList.add('hidden');
    }

    function editGear(id, name, brand, weight, category) {
        document.getElementById('gear-modal').classList.remove('hidden');
        document.getElementById('gear-form').action = "/gears/" + id;
        document.getElementById('gear-method').value = "PUT";
        document.getElementById('gear-modal-title').innerText = "Edit Gear";
        document.getElementById('gear-name').value = name;
        document.getElementById('gear-brand').value = brand;
        document.getElementById('gear-weight').value = weight;
        
        let catSelect = document.getElementById('gear-category');
        let options = Array.from(catSelect.options);
        if(!options.some(opt => opt.value === category)) {
            let newOption = new Option(category, category);
            catSelect.add(newOption);
        }
        catSelect.value = category;
    }
</script>
