@extends ('layouts.app')

@section('content')
    <div class="pt-32 bg-[#F8F9FA] min-h-screen">
        <form id="explore-form" method="GET" action="{{ route('explore') }}" class="w-[90%] mx-auto pb-20 flex flex-col lg:flex-row items-start gap-8">
            <aside
                class="flex flex-col gap-8 pb-4 mb-8 w-full lg:w-64 lg:sticky lg:top-32 lg:max-h-[calc(100vh-9rem)] lg:overflow-y-auto flex-shrink-0">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/explore/sort.png') }}" alt="Sort Icon" class="w-5 h-5 object-contain" />
                    <h2 class="font-bold text-lg text-[#001E3A]">
                        Refine Discovery
                    </h2>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-gray-400 mb-3 tracking-wider uppercase">
                        Difficulty
                    </h3>
                    <div class="flex flex-col gap-2">
                        @foreach (['easy', 'moderate', 'hard', 'strenuous'] as $difficulty)
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="difficulty[]" value="{{ $difficulty }}"
                                    @if(in_array($difficulty, request('difficulty', []))) checked @endif
                                    class="w-4 h-4 rounded border-gray-300 text-[#094174] focus:ring-[#094174]" />
                                <span class="text-sm text-[#1a2b4c] font-medium capitalize">{{ $difficulty }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h3 class="text-xs font-bold text-gray-400 mb-3 tracking-wider uppercase">Region</h3>
                    <div class="flex flex-col gap-2">
                        @foreach (['Sumatera', 'Jawa', 'Kalimantan', 'Sulawesi', 'Maluku', 'Papua', 'Bali & Nusa Tenggara'] as $region)
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="region[]" value="{{ $region }}"
                                    @if(in_array($region, request('region', []))) checked @endif
                                    class="w-4 h-4 rounded border-gray-300 text-[#094174] focus:ring-[#094174]">
                                <span class="text-sm text-[#1a2b4c] font-medium capitalize">{{ $region }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <button type="submit"
                    class="bg-[#094174] text-white font-bold text-sm py-2 px-6 rounded-full w-fit mt-2 hover:bg-[#105DA3] transition shadow-md">
                    Apply Filters
                </button>
            </aside>

            <div class="w-full min-w-0">
                <div class="mb-10 lg:ml-7">
                    <div class="flex items-center gap-3 bg-gray-200/60 rounded-full px-5 py-3 w-full">
                        <img src="{{ asset('images/explore/search.png') }}" alt="Search Icon"
                            class="w-4 h-4 object-contain opacity-70" />
                        <input id="search-input" type="text" name="search" value="{{ request('search') }}" placeholder="Find mountains"
                            class="bg-transparent border-none p-0 focus:outline-none focus:ring-0 w-full text-sm text-[#1a2b4c] placeholder-gray-500 font-medium" />
                        <button type="submit" class="hidden">Search</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 lg:ml-7">
                    @foreach ($mountains as $mountain)
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 flex flex-col h-full p-4">
                            @php
                                $imageUrl = $mountain->images->first()?->image_url;
                                $finalImage = !empty($imageUrl) ? $imageUrl : asset('images/dummymountain/rinjani.png');
                            @endphp
                            <img src="{{ $finalImage }}" alt="{{ $mountain->name }}"
                                class="w-full h-56 object-cover rounded-2xl" />

                            <div class="pt-5 pb-2 px-2 flex flex-col flex-grow">
                                <h3 class="font-bold text-xl text-[#1a2b4c] mb-1">
                                    {{ $mountain->name }}
                                </h3>

                                <div class="flex flex-wrap items-center gap-3 mb-3">
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

                                <a href="#"
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
