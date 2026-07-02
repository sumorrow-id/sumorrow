@extends ('layouts.app')

@section('content')
    <div class="pt-32 bg-[#F8F9FA] min-h-screen">
        <form id="explore-form" method="GET" action="{{ route('explore') }}" class="w-[95%] max-w-350 mx-auto pb-20 flex flex-col lg:flex-row items-start gap-8 lg:gap-12">
            <div id="filter-backdrop" class="fixed inset-0 bg-black/50 z-90 hidden lg:hidden"></div>

            <aside id="filter-drawer"
                class="fixed inset-y-0 right-0 z-100 w-80 bg-white shadow-2xl p-6 transform pl-8 translate-x-full transition-transform duration-300 overflow-y-auto lg:sticky lg:translate-x-0 lg:shadow-none lg:p-0 lg:bg-transparent lg:z-auto lg:w-64 lg:flex lg:flex-col lg:gap-5 lg:top-32 lg:h-fit lg:overflow-y-visible lg:shrink-0 lg:pl-2">

                <div class="flex items-center justify-between lg:justify-start gap-2 mb-6 lg:mb-0">
                    <div class="flex items-center gap-2 lg:hidden">
                        <h2 class="font-bold text-xl text-[#001E3A]">
                            Filter by
                        </h2>
                    </div>
                    <div class="hidden lg:flex items-center gap-2">
                        <img src="{{ asset('images/explore/sort.png') }}" alt="Sort Icon" class="w-5 h-5 object-contain" />
                        <h2 class="font-bold text-lg text-[#001E3A]">
                            Refine Discovery
                        </h2>
                    </div>
                    <button type="button" id="close-filter-btn" class="lg:hidden text-gray-500 hover:text-black">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mb-5 lg:mb-0">
                    <h3 class="text-xs font-bold text-gray-400 mb-2 tracking-wider uppercase">
                        Difficulty
                    </h3>
                    <div class="flex flex-col gap-1">
                        @foreach (['easy', 'moderate', 'hard', 'strenuous'] as $difficulty)
                            <label class="flex items-center gap-3 cursor-pointer py-1">
                                <input type="checkbox" name="difficulty[]" value="{{ $difficulty }}"
                                    @if(in_array($difficulty, request('difficulty', []))) checked @endif
                                    class="w-4 h-4 rounded border-gray-300 text-[#094174] focus:ring-[#094174]" />
                                <span class="text-sm text-[#1a2b4c] font-medium capitalize">{{ $difficulty }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h3 class="text-xs font-bold text-gray-400 mb-2 tracking-wider uppercase">Region</h3>
                    <div class="flex flex-col gap-1">
                        @foreach (['Sumatera', 'Jawa', 'Kalimantan', 'Sulawesi', 'Maluku', 'Papua', 'Bali & Nusa Tenggara'] as $region)
                            <label class="flex items-center gap-3 cursor-pointer py-1">
                                <input type="checkbox" name="region[]" value="{{ $region }}"
                                    @if(in_array($region, request('region', []))) checked @endif
                                    class="w-4 h-4 rounded border-gray-300 text-[#094174] focus:ring-[#094174]">
                                <span class="text-sm text-[#1a2b4c] font-medium capitalize">{{ $region }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 hidden lg:block">
                    <button type="submit"
                        class="bg-[#094174] text-white font-bold text-sm py-2 px-6 rounded-full w-fit hover:bg-[#105DA3] transition shadow-md">
                        Apply Filters
                    </button>
                </div>
            </aside>

            <div class="w-full min-w-0">
                <div class="mb-8 lg:mb-10 lg:ml-7 flex items-center gap-2">
                    <div class="flex items-center gap-3 bg-gray-200/60 rounded-full px-5 py-3 w-full">
                        <img src="{{ asset('images/explore/search.png') }}" alt="Search Icon"
                            class="w-4 h-4 object-contain opacity-70" />
                        <input id="search-input" type="text" name="search" value="{{ request('search') }}" placeholder="Find mountains"
                            class="bg-transparent border-none p-0 focus:outline-none focus:ring-0 w-full text-sm text-[#1a2b4c] placeholder-gray-500 font-medium" />
                        <button type="submit" class="hidden">Search</button>
                    </div>
                    <button type="button" id="mobile-filter-btn" class="lg:hidden flex items-center justify-center bg-gray-200/60 text-gray-500 rounded-full w-11 h-11 shrink-0 hover:bg-gray-300/60 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 lg:ml-7">
                    @foreach ($mountains as $mountain)
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 flex flex-col h-full p-4">
                            @php
                                $imageUrl = $mountain->images->first()?->image_url;
                                $finalImage = !empty($imageUrl) ? $imageUrl : asset('images/dummymountain/rinjani.png');
                            @endphp
                            <img src="{{ $finalImage }}" alt="{{ $mountain->name }}"
                                onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'"
                                class="w-full h-56 object-cover rounded-2xl" />

                            <div class="pt-5 pb-2 px-2 flex flex-col grow">
                                <h3 class="font-bold text-xl text-[#1a2b4c] mb-1">
                                    {{ $mountain->name }}
                                </h3>

                                <div class="flex flex-wrap items-center gap-3 mb-3">
                                    <div class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <span class="text-xs font-bold text-[#094174]">
                                            {{ number_format($mountain->avg_rating ?? 0, 1) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <img src="{{ asset('images/explore/mountainelevation.png') }}" alt="Elevation"
                                            class="h-4 w-4 object-contain" />
                                        <span class="text-xs font-bold text-[#094174]">{{ $mountain->elevation_masl }}
                                            mdpl</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#094174]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        </svg>
                                        <span class="text-xs font-bold text-[#094174] capitalize">{{ $mountain->difficulty }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#094174]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.242-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="text-xs font-bold text-[#094174]">{{ optional($mountain->province)->name ?? 'Unknown Region' }}</span>
                                    </div>
                                </div>

                                <p class="text-xs text-gray-500 mb-6 line-clamp-3 leading-relaxed mt-auto">
                                    {{ $mountain->description }}</p>

                                <a href="{{ route('explore.show', $mountain->id) }}"
                                    class="block w-full text-center bg-[#094174] hover:bg-[#105DA3] text-white font-bold py-2.5 rounded-full text-sm transition shadow-md mt-auto">
                                    Explore Now
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if ($mountains->hasPages())
                    <div class="explore-pagination mt-10 lg:ml-7">
                        {{ $mountains->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </form>
    </div>
@endsection
