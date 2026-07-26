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
                            {{ __('explore.filter_by') }}
                        </h2>
                    </div>
                    <div class="hidden lg:flex items-center gap-2">
                        <img src="{{ asset('images/explore/sort.png') }}" alt="{{ __('explore.sort_icon_alt') }}" class="w-5 h-5 object-contain" />
                        <h2 class="font-bold text-lg text-[#001E3A]">
                            {{ __('explore.refine_discovery') }}
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
                        {{ __('explore.difficulty') }}
                    </h3>
                    <div class="flex flex-col gap-1">
                        @foreach (['easy', 'moderate', 'hard', 'strenuous'] as $difficulty)
                            <label class="flex items-center gap-3 cursor-pointer py-1">
                                <input type="checkbox" name="difficulty[]" value="{{ $difficulty }}"
                                    @if(in_array($difficulty, request('difficulty', []))) checked @endif
                                    class="w-4 h-4 rounded border-gray-300 text-[#094174] focus:ring-[#094174]" />
                                <span class="text-sm text-[#1a2b4c] font-medium capitalize">{{ __('explore.difficulty_' . $difficulty) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h3 class="text-xs font-bold text-gray-400 mb-2 tracking-wider uppercase">{{ __('explore.region') }}</h3>
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

                <div class="pt-6 lg:pt-4">
                    <button type="submit"
                        class="bg-[#094174] text-white font-bold text-sm py-3 lg:py-2 px-6 rounded-full w-full lg:w-fit hover:bg-[#105DA3] transition shadow-md">
                        {{ __('explore.apply_filters') }}
                    </button>
                </div>
            </aside>

            <div class="w-full min-w-0">
                <div class="mb-8 lg:mb-10 lg:ml-7 flex items-center gap-2">
                    <div class="flex items-center gap-3 bg-gray-200/60 rounded-full px-5 py-3 w-full">
                        <img src="{{ asset('images/explore/search.png') }}" alt="{{ __('explore.search_icon_alt') }}"
                            class="w-4 h-4 object-contain opacity-70" />
                        <input id="search-input" type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('explore.find_mountains_placeholder') }}"
                            class="bg-transparent border-none p-0 focus:outline-none focus:ring-0 w-full text-sm text-[#1a2b4c] placeholder-gray-500 font-medium" />
                        <button type="submit" class="hidden">{{ __('explore.search') }}</button>
                    </div>
                    <button type="button" id="mobile-filter-btn" class="lg:hidden flex items-center justify-center bg-gray-200/60 text-gray-500 rounded-full w-11 h-11 shrink-0 hover:bg-gray-300/60 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </button>
                </div>

                {{-- Keep the visitor's location across filter submits (GET form drops params not backed by fields). --}}
                @if ($hasLocation)
                    <input type="hidden" name="lat" value="{{ request('lat') }}">
                    <input type="hidden" name="lng" value="{{ request('lng') }}">
                @endif

                {{-- Nearby Mountains — page-1 highlight only (Others is paginated). --}}
                @if ($otherMountains->onFirstPage())
                    @if ($hasLocation)
                        <section class="mb-12 lg:ml-7">
                            <h2 class="font-bold text-2xl text-[#001E3A] mb-1">{{ __('explore.nearby_mountains') }}</h2>
                            <p class="text-sm text-gray-500 mb-6">{{ __('explore.nearby_within_radius', ['radius' => $radiusKm]) }}</p>
                            @if ($nearbyMountains->isNotEmpty())
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @foreach ($nearbyMountains as $mountain)
                                        @include('explore.partials.mountain-card', ['mountain' => $mountain, 'showDistance' => true])
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 bg-white rounded-2xl border border-gray-100 p-6">
                                    {{ __('explore.no_nearby_within_radius', ['radius' => $radiusKm]) }}
                                </p>
                            @endif
                        </section>
                    @else
                        <div class="mb-12 lg:ml-7">
                            <button type="button" id="enable-location-btn"
                                class="flex items-center gap-2 bg-white border border-[#094174]/30 text-[#094174] font-bold text-sm px-5 py-3 rounded-full hover:bg-[#094174] hover:text-white transition shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.242-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ __('explore.enable_location') }}
                            </button>
                        </div>
                    @endif
                @endif

                {{-- Others --}}
                <section class="lg:ml-7">
                    @if ($hasLocation)
                        <h2 class="font-bold text-2xl text-[#001E3A] mb-6">{{ __('explore.others') }}</h2>
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @forelse ($otherMountains as $mountain)
                            @include('explore.partials.mountain-card', ['mountain' => $mountain, 'showDistance' => false])
                        @empty
                            <p class="text-sm text-gray-500 col-span-full">{{ __('explore.no_mountains_found') }}</p>
                        @endforelse
                    </div>
                    @if ($otherMountains->hasPages())
                        <div class="explore-pagination mt-10">
                            {{ $otherMountains->links() }}
                        </div>
                    @endif
                </section>
            </div>
        </form>
    </div>
@endsection
