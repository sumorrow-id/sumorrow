@php
    $heroImage = $heroImages[0] ?? 'https://images.unsplash.com/photo-1543886576-9d32d001cedc?auto=format&fit=crop&q=80&w=2000';
@endphp
@extends('layouts.app')

@section('content')
    <!-- Page wrapper for gradient background -->
    <div
        class="w-full overflow-hidden min-h-screen bg-gradient-to-b from-[#e8f0f6] to-[#f5f8fa] font-['Plus_Jakarta_Sans'] font-medium pb-16 sm:pb-24 text-[#001E3A]">

        <!-- Hero Section Container -->
        <div class="w-full sm:w-[95%] max-w-350 mx-auto pt-0 sm:pt-36">

            <!-- Hero Wrapper (box + search bar) -->
            <div class="relative">

                <!-- Hero Box -->
                <div
                    class="relative w-full h-[500px] sm:h-[700px] bg-gray-300 rounded-none sm:rounded-[2rem] shadow-xl flex items-center justify-center">
                    <!-- Two stacked layers crossfade between curated hero images -->
                    <img id="hero-image" src="{{ $heroImage }}" alt="{{ __('home.hero_image_alt') }}"
                        onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'"
                        class="absolute inset-0 w-full h-full object-cover brightness-75 rounded-none sm:rounded-[2rem] opacity-100 transition-opacity duration-[1200ms] ease-in-out">
                    <img id="hero-image-next" src="" alt="" aria-hidden="true"
                        class="absolute inset-0 w-full h-full object-cover brightness-75 rounded-none sm:rounded-[2rem] opacity-0 transition-opacity duration-[1200ms] ease-in-out">
                    <div class="absolute inset-0 bg-black/10 rounded-none sm:rounded-[2rem]"></div>

                    <!-- SUMORROW text -->
                    <h1
                        class="relative z-10 text-[min(10vw,140px)] font-['Bricolage_Grotesque'] font-black text-white leading-none tracking-tight">
                        SUMORROW
                    </h1>

                    <!-- Temperature Widget (Top Right Puzzle Cutout) -->
                    <a href="{{ $weatherData[0]['url'] ?? '#' }}" id="weather-widget"
                        data-weather="{{ json_encode($weatherData) }}" data-hero-images="{{ json_encode($heroImages) }}"
                        class="absolute bottom-0 left-0 right-0 sm:bottom-auto sm:left-auto sm:top-0 sm:right-0 bg-[#c2dbec]/95 border-0 sm:border-b-[16px] sm:border-l-[16px] border-[#e8f0f6] rounded-none sm:rounded-bl-[3rem] sm:rounded-tr-[2rem] w-full h-20 sm:w-56 sm:h-40 flex flex-col items-center justify-center p-3 sm:p-4 shadow-md transition-transform duration-500 hover:scale-[1.03] origin-top-right">
                        <div id="weather-content"
                            class="flex flex-col items-center justify-center text-center transition-opacity duration-500 opacity-100 w-full">
                            <span id="weather-location"
                                class="text-sm sm:text-base font-bold text-[#001E3A] line-clamp-1 leading-tight">{!! $weatherData[0]['loc'] ?? __('home.mountain_fallback') !!}</span>
                            <div class="flex items-center justify-center gap-1 sm:gap-2 mt-1">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-[#001E3A]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z" />
                                    <path d="M12 7v5" />
                                </svg>
                                <span id="weather-temp"
                                    class="text-3xl sm:text-4xl font-extrabold text-[#001E3A]">{!! $weatherData[0]['temp'] ?? '0&deg;' !!}</span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Bottom Search Bar (Puzzle Cutout) -->
                <div
                    class="relative w-full mt-6 sm:mt-0 sm:mx-auto sm:absolute sm:-bottom-10 sm:w-[95%] max-w-4xl sm:left-1/2 sm:-translate-x-1/2 z-20">
                    <form action="{{ url('/explore') }}" method="GET"
                        class="w-full bg-[#c2dbec] border-0 sm:border-[16px] border-[#e8f0f6] rounded-none sm:rounded-[3rem] p-4 sm:p-3 flex flex-col sm:flex-row items-center gap-3 sm:gap-6 shadow-sm">
                        <!-- Search Input -->
                        <div class="w-full sm:flex-1 relative bg-white/70 rounded-[1.5rem] sm:ml-2">
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" name="search" placeholder="{{ __('home.search_placeholder') }}"
                                class="w-full bg-transparent py-3 pl-12 pr-4 text-sm font-bold text-[#001E3A] placeholder-[#001E3A]/60 focus:outline-none rounded-[1.5rem]">
                        </div>

                        <!-- Divider -->
                        <div class="hidden sm:block w-[2px] h-10 bg-[#001E3A]/20"></div>

                        <!-- Filter Slider -->
                        <div class="w-full sm:w-1/3 flex flex-col pt-1 sm:pt-0 relative">
                            <div class="flex items-center gap-2 mb-2">
                                <label class="text-xs font-bold text-[#001E3A]">{{ __('home.elevation_filter_label') }}</label>
                                <span id="elevation-value"
                                    class="text-[10px] font-bold text-white bg-gray-500 px-1.5 py-0.5 rounded uppercase shadow-sm">{{ __('home.all_elevations') }}</span>
                            </div>
                            <input type="range" name="elevation" id="elevation-slider" min="0" max="5000"
                                step="100" value="0"
                                class="w-full h-1.5 bg-white rounded-lg appearance-none cursor-pointer range-slider outline-none focus:ring-2 focus:ring-[#094174] slider-thumb">
                        </div>

                        <!-- Explore Now Button -->
                        <button type="submit"
                            class="w-full sm:w-auto mt-2 sm:mt-0 px-6 py-2 border border-[#001E3A] text-[#001E3A] font-bold rounded-[1.5rem] hover:bg-[#001E3A] hover:text-white transition duration-300 whitespace-nowrap bg-transparent mr-2 text-sm">
                            {{ __('home.explore_now') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- About Us Section -->
            <div class="mt-24 sm:mt-32 mb-20 sm:mb-32 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
                <div class="col-span-1 sm:col-span-2 lg:col-span-4 mb-4 sm:mb-8 text-right">
                    {{-- Forced line breaks only from `sm` up — on phones the fixed
                         breaks fought the natural wrap and produced ragged lines. --}}
                    <h2
                        class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#001E3A] max-w-2xl ml-auto leading-tight">
                        {{ __('home.about_heading_line1') }}<br>{{ __('home.about_heading_line2') }}<br>{{ __('home.about_heading_line3') }}
                    </h2>
                </div>

                <!-- Cards -->
                @php
                    $features = [
                        [
                            'icon' =>
                                '<svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96z"/></svg>',
                            'title' => __('home.feature_realtime_title'),
                            'desc' => __('home.feature_realtime_desc'),
                        ],
                        [
                            'icon' =>
                                '<svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>',
                            'title' => __('home.feature_community_title'),
                            'desc' => __('home.feature_community_desc'),
                        ],
                        [
                            'icon' =>
                                '<svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M20.5 3l-.16.03L15 5.1 9 3 3.36 4.9c-.21.07-.36.25-.36.48V20.5c0 .28.22.5.5.5l.16-.03L9 18.9l6 2.1 5.64-1.9c.21-.07.36-.25.36-.48V3.5c0-.28-.22-.5-.5-.5zM10 5.47l4 1.4v11.66l-4-1.4V5.47zm-5 1.56l3-1.01v11.69l-3 1.01V7.03zm14 9.94l-3 1.01V6.29l3-1.01v11.69z"/></svg>',
                            'title' => __('home.feature_routes_title'),
                            'desc' => __('home.feature_routes_desc'),
                        ],
                        [
                            'icon' =>
                                '<svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M19.43 12.98c.04-.32.07-.64.07-.98s-.03-.66-.07-.98l2.11-1.65c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.3-.61-.22l-2.49 1c-.52-.4-1.08-.73-1.69-.98l-.38-2.65C14.46 2.18 14.25 2 14 2h-4c-.25 0-.46.18-.49.42l-.38 2.65c-.61.25-1.17.59-1.69.98l-2.49-1c-.23-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49-.12-.64l2.11 1.65c-.04.32-.07.65-.07.98s.03.66.07.98l-2.11 1.65c-.19.15-.24.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1c.52.4 1.08.73 1.69.98l.38 2.65c.03.24.24.42.49.42h4c.25 0 .46-.18.49-.42l.38-2.65c.61-.25 1.17-.59 1.69-.98l2.49 1c.23.09.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.65zm-7.43 2.52c-1.93 0-3.5-1.57-3.5-3.5s1.57-3.5 3.5-3.5 3.5 1.57 3.5 3.5-1.57 3.5-3.5 3.5z"/></svg>',
                            'title' => __('home.feature_gear_title'),
                            'desc' => __('home.feature_gear_desc'),
                        ],
                    ];
                @endphp
                @foreach ($features as $feature)
                    <div
                        class="bg-[#c2dbec] rounded-none sm:rounded-3xl p-6 sm:p-8 flex flex-col hover:-translate-y-2 transition-transform duration-300 shadow-sm relative group overflow-hidden">
                        <div
                            class="absolute -right-4 -top-4 w-24 h-24 bg-white/20 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500">
                        </div>
                        <div
                            class="w-12 h-12 bg-[#094174] text-white rounded-xl flex items-center justify-center mb-6 shadow-md z-10">
                            {!! $feature['icon'] !!}
                        </div>
                        <h3 class="text-xl font-bold text-[#001E3A] mb-3 z-10">{{ $feature['title'] }}</h3>
                        <p class="text-sm font-semibold text-[#001E3A]/70 leading-relaxed z-10">{{ $feature['desc'] }}</p>
                    </div>
                @endforeach
            </div>

            <!-- Quotes Section -->
            <div class="flex justify-end pt-5 pb-5 mb-10 sm:mb-16 pr-4 lg:pr-12 w-full">
                <h2
                    class="text-4xl sm:text-5xl md:text-6xl font-['Newsreader'] font-semibold italic bg-gradient-to-r from-[#001E3A] to-[#4286f4] text-transparent bg-clip-text tracking-tight text-left">
                    <span class="block">{{ __('home.tagline_line1') }}</span>
                    <span class="block ml-[3.5em] lg:ml-[5.5em]">{{ __('home.tagline_line2') }}</span>
                </h2>
            </div>

            <!-- Popular Mountains Section -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-12 items-center mb-20 sm:mb-32 relative">

                <!-- Background Decoration -->
                <div
                    class="absolute -left-1/4 top-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-[#c2dbec]/30 rounded-full blur-3xl -z-10">
                </div>

                <!-- Left Text -->
                <div class="lg:col-span-4 z-10 ml-4 sm:ml-0">
                    <h2 class="text-4xl sm:text-5xl font-extrabold text-[#001E3A] mb-8 leading-tight">
                        {{ __('home.popular_mountains_heading_line1') }}<br>{{ __('home.popular_mountains_heading_line2') }}<br>{{ __('home.popular_mountains_heading_line3') }}
                    </h2>
                    <a href="{{ url('/explore') }}"
                        class="inline-flex items-center gap-3 px-6 py-2 border border-[#001E3A] text-[#001E3A] font-bold rounded-full hover:bg-[#001E3A] hover:text-white transition duration-300">
                        {{ __('home.explore_more') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>

                <!-- Carousel Gallery Right -->
                <div class="lg:col-span-8 relative z-10 h-[380px] sm:h-[450px]">
                    <div id="mountain-carousel-container" class="relative w-full h-[330px] sm:h-[400px]">
                        @foreach ($popularMountains as $idx => $popMt)
                            <a href="{{ url('/explore/' . $popMt['id']) }}"
                                class="mtn-card overflow-hidden group shadow-lg cursor-pointer block transition-all duration-700 ease-in-out absolute right-0 top-1/2 w-[160px] sm:w-[200px] h-[220px] sm:h-[260px] rounded-none sm:rounded-[2rem]"
                                data-index="{{ $idx }}">
                                <img src="{{ $popMt['image'] }}"
                                    onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'"
                                    class="absolute inset-0 w-full h-full object-cover transition duration-500 group-hover:scale-110"
                                    alt="{{ $popMt['name'] }}">
                                <div
                                    class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-[#001E3A] to-transparent p-6 pt-24 z-10 flex justify-between items-end">
                                    <div>
                                        <h3
                                            class="text-white text-xl font-bold transition duration-300 translate-y-0 group-hover:-translate-y-1">
                                            {{ $popMt['name'] }}</h3>
                                        <p
                                            class="text-white/80 font-semibold text-sm translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                                            {{ $popMt['location'] }}</p>
                                    </div>
                                    <div
                                        class="w-10 h-10 shrink-0 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-white group-hover:bg-white group-hover:text-[#001E3A] transition-all duration-300 translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 mb-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                        </svg>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="absolute right-0 bottom-0 flex gap-2 z-20">
                        <button id="btn-prev-mtn"
                            class="w-10 h-10 rounded-full bg-[#094174] text-white flex items-center justify-center hover:opacity-80 transition shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <button id="btn-next-mtn"
                            class="w-10 h-10 rounded-full bg-[#094174] text-white flex items-center justify-center hover:opacity-80 transition shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Community Section -->
            <div
                class="w-full bg-[#c2dbec] rounded-none sm:rounded-[4rem] border border-[#094174]/20 p-6 sm:p-12 lg:p-16 flex flex-col lg:flex-row gap-8 lg:gap-12 mb-20 sm:mb-32 items-center relative overflow-hidden shadow-sm">

                <!-- Bg pattern -->
                <div class="absolute inset-0 opacity-10 pointer-events-none"
                    style="background-image: radial-gradient(#094174 1px, transparent 1px); background-size: 24px 24px;">
                </div>

                <div class="lg:w-1/3 z-10">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black text-[#001E3A] mb-6">{{ __('home.community_heading') }}</h2>
                    <p class="text-[#001E3A]/70 font-semibold mb-8 text-lg">
                        {{ __('home.community_desc') }}
                    </p>
                    <a href="{{ url('/community') }}"
                        class="hidden lg:inline-flex items-center gap-3 px-8 py-3 border border-[#001E3A] text-[#001E3A] font-bold rounded-full hover:bg-[#001E3A] hover:text-white transition duration-300 bg-transparent">
                        {{ __('home.join_now') }}
                        <svg class="w-4 h-4 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>

                <div class="lg:w-2/3 w-full relative z-10">
                    <div class="flex overflow-x-auto snap-x snap-mandatory gap-6 pb-12 hide-scrollbar"
                        id="community-gallery">
                        <!-- Community Card -->
                        @foreach ($communityCards as $card)
                            <a href="{{ $card['url'] }}"
                                class="snap-start shrink-0 w-[260px] sm:w-[320px] bg-white rounded-3xl p-5 shadow-sm border border-gray-100 block hover:-translate-y-1 transition-transform">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $card['avatar'] }}" alt="{{ $card['username'] }}"
                                            class="w-10 h-10 rounded-full object-cover shrink-0">
                                        <span class="font-bold text-[#001E3A] text-sm flex-1">{{ $card['username'] }}</span>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                                    </svg>
                                </div>
                                <p class="text-xs sm:text-sm font-semibold text-[#001E3A] leading-relaxed mb-4">
                                    {{ $card['body'] }}
                                </p>
                                <div
                                    class="w-full h-40 sm:h-48 bg-gray-200 rounded-2xl mb-4 flex items-center justify-center overflow-hidden">
                                    <img src="{{ $card['image'] }}"
                                        onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'"
                                        class="w-full h-full object-cover">
                                </div>
                                <div class="flex items-center gap-4 text-gray-500 text-xs sm:text-sm font-semibold">
                                    <div class="flex items-center gap-1"><svg class="w-4 h-4 sm:w-5 sm:h-5"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                            </path>
                                        </svg> {{ $card['likes'] }}</div>
                                    <div class="flex items-center gap-1"><svg class="w-4 h-4 sm:w-5 sm:h-5"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                            </path>
                                        </svg> {{ $card['comments'] }}</div>
                                    <div class="flex items-center gap-1 ml-auto"><svg class="w-4 h-4 sm:w-5 sm:h-5"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                        </svg></div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <!-- Navigation -->
                    <div class="hidden sm:flex absolute right-0 -bottom-2 gap-3 z-20">
                        <button type="button"
                            class="w-10 h-10 rounded-full bg-white text-[#094174] flex items-center justify-center shadow-md hover:bg-gray-100 transition"
                            onclick="document.getElementById('community-gallery').scrollBy({left:-320, behavior:'smooth'})">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <button type="button"
                            class="w-10 h-10 rounded-full bg-white text-[#094174] flex items-center justify-center shadow-md hover:bg-gray-100 transition"
                            onclick="document.getElementById('community-gallery').scrollBy({left:320, behavior:'smooth'})">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile-only: Join Now moved below the community cards -->
                <a href="{{ url('/community') }}"
                    class="lg:hidden z-10 w-full inline-flex items-center justify-center gap-3 px-8 py-3 border border-[#001E3A] text-[#001E3A] font-bold rounded-full hover:bg-[#001E3A] hover:text-white transition duration-300 bg-transparent">
                    {{ __('home.join_now') }}
                    <svg class="w-4 h-4 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>
            </div>

            <!-- Choose Your Peak Section -->
            <div>
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
                    <div class="mx-4 px-2 sm:mx-0 sm:px-0">
                        <h2 class="text-4xl sm:text-5xl font-extrabold text-[#001E3A] mb-3">{{ __('home.choose_your_peak_heading') }}</h2>
                        <p class="text-[#001E3A]/60 font-semibold text-sm sm:text-base max-w-md">
                            {{ __('home.choose_your_peak_desc') }}
                        </p>
                    </div>
                    <a href="{{ url('/explore') }}"
                        class="hidden sm:inline-flex items-center gap-2 px-6 py-2 border border-[#001E3A] text-[#001E3A] font-bold rounded-full hover:bg-[#001E3A] hover:text-white transition duration-300 whitespace-nowrap">
                        {{ __('home.explore_more') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>

                <!-- Peak Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    @foreach ($randomPeaks as $peak)
                        <a href="{{ url('/explore/' . $peak['id']) }}"
                            class="bg-[#c2dbec] rounded-none sm:rounded-[2rem] p-5 shadow-sm relative group cursor-pointer transition-transform hover:-translate-y-2 flex flex-col h-full">
                            <!-- Top Info -->
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="font-bold text-[#001E3A] text-lg">{{ $peak['name'] }}</h3>
                                    <p class="text-[#001E3A]/60 text-sm font-semibold">{{ $peak['location'] }}</p>
                                </div>
                                <div
                                    class="w-10 h-10 rounded-full bg-[#001E3A] text-white flex items-center justify-center shrink-0 shadow-md transform group-hover:-rotate-45 transition-transform duration-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Image -->
                            <div class="w-full h-48 bg-gray-300 rounded-2xl mb-4 overflow-hidden relative mt-auto">
                                <img src="{{ $peak['image'] }}"
                                    onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    alt="{{ $peak['name'] }}">
                                <div class="absolute inset-0 bg-black/5"></div>
                            </div>

                            <!-- Bottom Info -->
                            <div class="flex items-center gap-4 text-xs font-bold text-[#001E3A]">
                                <div
                                    class="flex items-center gap-1.5 bg-white/50 px-3 py-1.5 rounded-full backdrop-blur-md">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M14 6l-3.38 4H16v2h-6.73L5 18H2.5l5.5-6.5-.47-.56-2.5 3-1.52-1.8L9 6h5zM15 16l6-7 1.5 1.5-6 7L15 16z" />
                                    </svg>
                                    {{ $peak['elevation'] ? number_format($peak['elevation']).' '.__('explore.unit_masl') : __('explore.unknown_elevation') }}
                                </div>
                                <div
                                    class="flex items-center gap-1.5 bg-white/50 px-3 py-1.5 rounded-full backdrop-blur-md border border-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                    {{ __('explore.difficulty_'.$peak['difficulty']) }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Mobile-only: Explore More moved below the peak cards -->
                <a href="{{ url('/explore') }}"
                    class="sm:hidden mt-8 mx-4 flex items-center justify-center gap-2 px-6 py-3 border border-[#001E3A] text-[#001E3A] font-bold rounded-full hover:bg-[#001E3A] hover:text-white transition duration-300 whitespace-nowrap">
                    {{ __('home.explore_more') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>
            </div>

        </div>
    </div>

@endsection
