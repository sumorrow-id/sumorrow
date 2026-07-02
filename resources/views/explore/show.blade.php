@extends('layouts.app')

@section('content')
    <div class="bg-[#F8F9FA] min-h-screen">
        <!-- Hero Section -->
        @php
            $heroImage = $mountain->images->first()
                ? $mountain->images->first()->image_url
                : asset('images/default-mountain.jpg');
        @endphp
        <div class="relative w-full h-[50vh] md:h-[60vh] lg:h-[70vh]">
            <img src="{{ $heroImage }}" alt="{{ $mountain->name }}" onerror="this.onerror=null;this.src='{{ asset('images/default-mountain.jpg') }}'" class="w-full h-full object-cover" />
            <div class="absolute inset-0 bg-black/40"></div>
            <div
                class="absolute bottom-0 left-0 w-full p-6 md:p-12 lg:px-24 bg-linear-to-t from-black/80 to-transparent flex justify-between items-end gap-4">
                <div class="min-w-0">
                    <h1 class="text-2xl sm:text-3xl md:text-5xl lg:text-6xl font-bold text-white mb-2 break-words">
                        {{ $mountain->name }}</h1>
                    <div class="flex flex-wrap items-center gap-4 text-white/90 text-sm md:text-base">
                        <span id="hero-location-pin" role="button" tabindex="0" title="View on map" class="flex items-center gap-1 cursor-pointer transition-colors duration-300 hover:text-white hover:drop-shadow-[0_0_8px_rgba(239,68,68,0.8)]">
                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            {{ $mountain->province->name ?? 'Indonesia' }}
                        </span>
                        <span class="flex items-center gap-1 bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm">
                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            {{ number_format($mountain->avg_rating, 1) }} ({{ $mountain->ratings->count() }} reviews)
                        </span>
                        <span class="bg-[#001E3A] text-white px-3 py-1 rounded-full font-semibold">
                            {{ ucfirst($mountain->difficulty) }}
                        </span>
                    </div>
                </div>

                <!-- Weather Card -->
                <div id="hero-weather-card" role="button" tabindex="0" title="See full forecast"
                    class="hidden shrink-0 flex-col items-center justify-center bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-3 sm:p-4 min-w-[88px] sm:min-w-[100px] text-white shadow-lg cursor-pointer transition-all duration-300 hover:bg-white/20 hover:-translate-y-1 hover:shadow-xl">
                    <span class="text-xs font-medium text-white/80 uppercase tracking-wider mb-1"
                        id="hero-weather-desc"></span>
                    <div id="hero-weather-icon-container"></div>
                    <div class="mt-1 font-bold text-xl md:text-2xl" id="hero-weather-temp"></div>
                    <span class="mt-1 text-[10px] text-white/60 uppercase tracking-wider">Tap for forecast</span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            <div class="flex flex-col lg:flex-row gap-8">

                <!-- Left Column: Details -->
                <div class="w-full lg:w-2/3 space-y-12">

                    <!-- About Section -->
                    <section>
                        <h2 class="text-2xl font-bold text-[#001E3A] mb-4">About</h2>
                        <p class="text-gray-700 leading-relaxed text-justify">
                            {{ $mountain->description ?: 'No description available for this mountain yet. Explore its beauty and share your experiences!' }}
                        </p>
                    </section>

                    <!-- Critical Information -->
                    <section>
                        <h2 class="text-2xl font-bold text-[#001E3A] mb-4">Critical Information</h2>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div
                                class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-[#2A9D8F]">
                                <span class="text-sm text-gray-500 mb-1">Height</span>
                                <span
                                    class="text-xl font-bold text-[#2A9D8F]">{{ number_format($mountain->elevation_masl) }}
                                    <span class="text-sm font-normal">masl</span></span>
                            </div>
                            <div
                                class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-[#2A9D8F]">
                                <span class="text-sm text-gray-500 mb-1">Length</span>
                                <span class="text-xl font-bold text-[#2A9D8F]">{{ $mountain->length_km ?? '-' }} <span
                                        class="text-sm font-normal">km</span></span>
                            </div>
                            <div
                                class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-[#2A9D8F]">
                                <span class="text-sm text-gray-500 mb-1">Elevation Gain</span>
                                <span class="text-xl font-bold text-[#2A9D8F]">{{ $mountain->elevation_gain_m ?? '-' }}
                                    <span class="text-sm font-normal">m</span></span>
                            </div>
                            @php
                                // Naismith's rule calculation for rough estimate (length in km / 3 km/h + gain in meters / 300m/h)
                                $estHours = 0;
                                if ($mountain->length_km && $mountain->elevation_gain_m) {
                                    $estHours = ceil($mountain->length_km / 3 + $mountain->elevation_gain_m / 300);
                                }
                            @endphp
                            <div
                                class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-[#2A9D8F]">
                                <span class="text-sm text-gray-500 mb-1">Est. Time</span>
                                <span class="text-xl font-bold text-[#2A9D8F]">{{ $estHours > 0 ? $estHours : '-' }} <span
                                        class="text-sm font-normal">hrs</span></span>
                            </div>
                        </div>
                    </section>

                    <!-- Weather Forecasting (OpenWeatherMap) -->
                    @php
                        // Fungsi pembantu untuk mengubah format "8 deg 16' 0" S" menjadi desimal
                        $dmsToDecimal = function ($dmsStr) {
                            $dmsStr = trim(preg_replace('/\s+/', ' ', $dmsStr));

                            // Regex untuk menangkap derajat, menit, detik, dan arah
                            if (preg_match('/(\d+)\s*deg\s*(\d+)\s*\'\s*(\d+)\s*"\s*([NSEW])/i', $dmsStr, $matches)) {
                                $degrees = (float) $matches[1];
                                $minutes = (float) $matches[2];
                                $seconds = (float) $matches[3];
                                $direction = strtoupper($matches[4]);

                                $decimal = $degrees + $minutes / 60 + $seconds / 3600;

                                if ($direction === 'S' || $direction === 'W') {
                                    $decimal = -$decimal;
                                }

                                return number_format($decimal, 6, '.', '');
                            }
                            return null;
                        };

                        // Pecah koordinat database menjadi bagian Lat dan Lng
                        // Format asli DB: 8 deg 16' 0" S, 115 deg 25' 0" E
                        $parts = explode(',', $mountain->coordinates);

                        $lat = isset($parts[0]) ? $dmsToDecimal($parts[0]) : '-8.2666'; // Default fallback
                        $lng = isset($parts[1]) ? $dmsToDecimal($parts[1]) : '115.4166';
                    @endphp

                    <section id="weather-forecast" class="scroll-mt-24">
                        <h2 class="text-2xl font-bold text-[#001E3A] mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z">
                                </path>
                            </svg>
                            Weather Forecast
                        </h2>
                        <div id="weather-container" class="grid grid-cols-1 md:grid-cols-3 gap-4"
                            data-weather-url="{{ $weatherUrl }}" data-forecast-url="{{ $forecastUrl }}">
                            <div class="col-span-3 text-center py-6 text-gray-500">
                                Loading weather forecast...
                            </div>
                        </div>
                    </section>

                    <!-- Map Location -->
                    <section id="map-location" class="scroll-mt-24">
                        <h2 class="text-2xl font-bold text-[#001E3A] mb-4">Location</h2>
                        <div class="bg-white p-2 rounded-xl shadow-sm border border-gray-100 h-100 w-full overflow-hidden">
                            <iframe width="100%" height="100%" frameborder="0" style="border:0; border-radius: 0.5rem;"
                                src="https://maps.google.com/maps?q={{ $lat }},{{ $lng }}&t=&z=12&ie=UTF8&iwloc=&output=embed"
                                allowfullscreen>
                            </iframe>
                        </div>
                    </section>

                    <!-- Reviews Section -->
                    <section>
                        <h2 class="text-2xl font-bold text-[#001E3A] mb-4">Reviews ({{ $mountain->ratings->count() }})</h2>

                        <!-- Add Review Form -->
                        @auth
                            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Write a Review</h3>
                                <form action="{{ route('explore.ratings.store', $mountain->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                                        <div class="flex gap-2 flex-row-reverse justify-end">
                                            @for ($i = 5; $i >= 1; $i--)
                                                <input type="radio" id="star-{{ $i }}" name="score"
                                                    value="{{ $i }}" class="peer hidden" required>
                                                <label for="star-{{ $i }}"
                                                    class="cursor-pointer text-gray-200 peer-checked:text-yellow-400 hover:text-yellow-400 [&:hover~label]:text-yellow-400 transition-colors">
                                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                        </path>
                                                    </svg>
                                                </label>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="review" class="block text-sm font-medium text-gray-700 mb-1">Your
                                            Experience</label>
                                        <textarea id="review" name="review" rows="3"
                                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#001E3A] focus:border-[#001E3A] p-3 text-sm border"
                                            placeholder="Tell us about your hike..."></textarea>
                                    </div>
                                    <button type="submit"
                                        class="bg-[#001E3A] text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-900 transition-colors">Submit
                                        Review</button>
                                </form>
                            </div>
                        @else
                            <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 text-center mb-6">
                                <p class="text-gray-600 mb-3">Login to share your experience on this mountain.</p>
                                <a href="{{ route('showLogin') }}"
                                    class="inline-block bg-[#001E3A] text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-900 transition-colors">Login</a>
                            </div>
                        @endauth

                        <div class="space-y-4">
                            @forelse($mountain->ratings->sortByDesc('created_at')->take(5) as $rating)
                                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div
                                            class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-[#001E3A] font-bold text-sm uppercase">
                                            {{ substr($rating->user->username ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">
                                                {{ $rating->user->username ?? 'Unknown Hiker' }}</h4>
                                            <div class="flex text-yellow-400 text-xs mt-1">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $rating->score ? 'text-yellow-400' : 'text-gray-200' }}"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                        </path>
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                    @if ($rating->review)
                                        <p class="text-gray-600 mt-2 text-sm">{{ $rating->review }}</p>
                                    @endif
                                </div>
                            @empty
                                <div
                                    class="bg-white p-6 rounded-xl border border-dashed border-gray-300 text-center text-gray-500">
                                    No reviews yet. Be the first to conquer and review this mountain!
                                </div>
                            @endforelse
                        </div>
                    </section>

                    <!-- FAQ Section -->
                    <section>
                        <h2 class="text-2xl font-bold text-[#001E3A] mb-4">FAQ</h2>
                        <div class="space-y-3">
                            <!-- FAQ 1 -->
                            <div class="bg-white border text-left border-gray-200 rounded-lg overflow-hidden group">
                                <input type="checkbox" id="faq1" class="peer hidden">
                                <label for="faq1"
                                    class="flex justify-between items-center p-4 cursor-pointer font-semibold text-gray-800 hover:bg-gray-50">
                                    <span>Do I need a permit to climb {{ $mountain->name }}?</span>
                                    <svg class="w-5 h-5 transition-transform duration-300 peer-checked:rotate-180"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </label>
                                <div
                                    class="max-h-0 peer-checked:max-h-40 overflow-hidden transition-all duration-300 bg-gray-50">
                                    <p class="p-4 text-gray-600 text-sm border-t border-gray-100">
                                        Yes, most mountains in Indonesia require a SIMAKSI (registration/permit) before
                                        climbing. You should check with the local basecamp or national park authority for
                                        online registration details.
                                    </p>
                                </div>
                            </div>

                            <!-- FAQ 2 -->
                            <div class="bg-white border text-left border-gray-200 rounded-lg overflow-hidden group">
                                <input type="checkbox" id="faq2" class="peer hidden">
                                <label for="faq2"
                                    class="flex justify-between items-center p-4 cursor-pointer font-semibold text-gray-800 hover:bg-gray-50">
                                    <span>What is the best time to hike?</span>
                                    <svg class="w-5 h-5 transition-transform duration-300 peer-checked:rotate-180"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </label>
                                <div
                                    class="max-h-0 peer-checked:max-h-40 overflow-hidden transition-all duration-300 bg-gray-50">
                                    <p class="p-4 text-gray-600 text-sm border-t border-gray-100">
                                        The dry season (May to September) is generally the best time to hike. Avoid the
                                        rainy season as trails become slippery and visibility is often poor.
                                    </p>
                                </div>
                            </div>

                            <!-- FAQ 3 -->
                            <div class="bg-white border text-left border-gray-200 rounded-lg overflow-hidden group">
                                <input type="checkbox" id="faq3" class="peer hidden">
                                <label for="faq3"
                                    class="flex justify-between items-center p-4 cursor-pointer font-semibold text-gray-800 hover:bg-gray-50">
                                    <span>Is {{ $mountain->name }} safe for beginners?</span>
                                    <svg class="w-5 h-5 transition-transform duration-300 peer-checked:rotate-180"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </label>
                                <div
                                    class="max-h-0 peer-checked:max-h-40 overflow-hidden transition-all duration-300 bg-gray-50">
                                    <p class="p-4 text-gray-600 text-sm border-t border-gray-100">
                                        This depends on the difficulty level. Rated as
                                        "{{ ucfirst($mountain->difficulty) }}", you should prepare adequately. Always hike
                                        with experienced companions if you are new to mountaineering.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>

                <!-- Right Column: Sidebar (Nearby Mountains & Basecamps) -->
                <div class="w-full lg:w-1/3 space-y-8">

                    @if ($mountain->basecamps->count() > 0)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h3 class="font-bold text-[#001E3A] text-xl mb-4">Official Basecamps</h3>
                            <ul class="space-y-4">
                                @foreach ($mountain->basecamps as $basecamp)
                                    <li
                                        class="flex items-center gap-3 pb-4 border-b border-gray-50 last:border-0 last:pb-0">
                                        <div class="bg-[#2A9D8F]/10 p-2 rounded-lg text-[#2A9D8F]">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                                </path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $basecamp->name }}</h4>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($nearbyMountains->count() > 0)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h3 class="font-bold text-[#001E3A] text-xl mb-4">Nearby Mountains</h3>
                            <div class="space-y-4">
                                @foreach ($nearbyMountains as $nearby)
                                    <a href="{{ route('explore.show', $nearby->id) }}"
                                        class="group flex min-h-20 gap-3 hover:bg-gray-50 p-2 rounded-xl transition-colors">
                                        @php
                                            $thumb = $nearby->images->first()
                                                ? $nearby->images->first()->image_url
                                                : asset('images/default-mountain.jpg');
                                        @endphp
                                        <img src="{{ $thumb }}" alt="{{ $nearby->name }}"
                                            onerror="this.onerror=null;this.src='{{ asset('images/default-mountain.jpg') }}'"
                                            class="w-20 h-20 rounded-lg object-cover bg-gray-100" />
                                        <div class="flex-1 py-1">
                                            <h4
                                                class="font-semibold text-gray-900 group-hover:text-[#2A9D8F] transition-colors">
                                                {{ $nearby->name }}</h4>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ number_format($nearby->elevation_masl) }} masl</p>
                                            <div class="flex items-center text-yellow-400 text-xs mt-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                <span
                                                    class="ml-1 text-gray-600">{{ number_format($nearby->avg_rating, 1) }}</span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div
                        class="bg-linear-to-br from-[#001E3A] to-[#003865] rounded-2xl shadow-sm text-white p-6 relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white/10 blur-2xl"></div>
                        <h3 class="font-bold text-xl mb-2">Ready to conquer?</h3>
                        <p class="text-sm text-white/80 mb-6">Log your hike, track your achievements, and share your
                            journey with the community.</p>
                        <a href="{{ route('community') }}"
                            class="block text-center w-full bg-[#2A9D8F] hover:bg-[#21867a] text-white font-semibold py-3 rounded-xl transition-colors">
                            Join Community
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Back to Top Button -->
    <button id="back-to-top" type="button" aria-label="Back to top"
        class="fixed bottom-5 right-5 sm:bottom-8 sm:right-8 z-50 flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-full bg-[#001E3A] text-white shadow-lg ring-1 ring-white/10 opacity-0 translate-y-4 scale-90 pointer-events-none transition-all duration-300 ease-out hover:bg-[#003865] hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-[#2A9D8F] active:scale-95">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
        </svg>
    </button>

@endsection
