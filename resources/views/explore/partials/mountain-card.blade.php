@php
    $imageUrl = $mountain->images->first()?->image_url;
    $finalImage = !empty($imageUrl) ? $imageUrl : asset('images/default-mountain.jpg');
    $showDistance = ($showDistance ?? false) && $mountain->distance_km !== null;
@endphp
<div class="bg-white rounded-3xl shadow-sm border border-gray-100 flex flex-col h-full p-4">
    <div class="relative">
        <img src="{{ $finalImage }}" alt="{{ $mountain->name }}" loading="lazy"
            onerror="this.onerror=null;this.src='{{ asset('images/default-mountain.jpg') }}'"
            class="w-full h-56 object-cover rounded-2xl" />
        @if ($showDistance)
            <span class="absolute top-3 left-3 flex items-center gap-1 bg-[#094174] text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.242-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ number_format($mountain->distance_km, 0) }} {{ __('explore.km_away') }}
            </span>
        @endif
    </div>

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
                <img src="{{ asset('images/explore/mountainelevation.png') }}" alt="{{ __('explore.elevation_alt') }}"
                    class="h-4 w-4 object-contain" />
                <span class="text-xs font-bold text-[#094174]">{{ $mountain->elevation_masl }}
                    mdpl</span>
            </div>
            <div class="flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#094174]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <span class="text-xs font-bold text-[#094174] capitalize">{{ $mountain->difficulty && \Illuminate\Support\Facades\Lang::has('explore.difficulty_' . $mountain->difficulty) ? __('explore.difficulty_' . $mountain->difficulty) : $mountain->difficulty }}</span>
            </div>
            <div class="flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#094174]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.242-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-xs font-bold text-[#094174]">{{ optional($mountain->province)->name ?? __('explore.unknown_region') }}</span>
            </div>
        </div>

        <p class="text-xs text-gray-500 mb-6 line-clamp-3 leading-relaxed mt-auto">
            {{ $mountain->description }}</p>

        <a href="{{ route('explore.show', $mountain->id) }}"
            class="block w-full text-center bg-[#094174] hover:bg-[#105DA3] text-white font-bold py-2.5 rounded-full text-sm transition shadow-md mt-auto">
            {{ __('explore.explore_now') }}
        </a>
    </div>
</div>
