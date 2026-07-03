<div class="grid grid-cols-1 lg:grid-cols-[1fr_350px] gap-8 mt-6">

    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-end mb-6">
            <h2 class="text-2xl font-bold text-[#0F172A]">Hiking History</h2>
            <div class="flex flex-wrap gap-x-4 gap-y-2 items-center">
                <a href="{{ route('profile.posts.index') }}" class="text-sm font-bold text-[#2A5C9A] hover:underline whitespace-nowrap">View All Activities</a>
                <a href="{{ route('profile.posts.create') }}"
                    class="bg-[#094174] hover:bg-[#105DA3] text-white text-sm font-bold py-2 px-4 rounded-full transition shadow-md hover:shadow-lg hover:-translate-y-0.5 whitespace-nowrap">+
                    New Activity</a>
            </div>
        </div>

        @forelse($posts as $post)
            <div
                class="bg-white rounded-3xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] flex flex-col justify-between">

                <div class="mb-6">
                    <div>
                        <div class="flex items-center justify-between gap-3 w-full">
                            <h3 class="text-[22px] font-bold text-[#0F172A] break-words">
                                {{ $post->mountain->name ?? $post->title }}
                            </h3>
                            @if ($post->duration_days)
                                <span
                                    class="bg-[#BDE0FE] text-[#1E40AF] text-[11px] font-semibold px-3 py-1 rounded-full whitespace-nowrap">
                                    {{ $post->duration_days }}D Expedition
                                </span>
                            @endif
                        </div>

                        <p class="text-[13px] text-gray-500 mt-1">
                            {{ $post->mountain?->province?->name ?? 'Indonesia' }} •
                            {{ $post->climbing_date ? $post->climbing_date->format('M d, Y') : $post->created_at->format('M d, Y') }}
                        </p>

                        @if ($post->route_name || isset($post->mountain->route))
                            <div class="flex items-center gap-2 mt-3 text-sm text-gray-600">
                                <img src="{{ asset('images/profile/route.png') }}" alt="Route"
                                    class="w-4 h-4 object-contain">
                                <span>Route:
                                    {{ $post->route_name ?? ($post->mountain->route ?? 'Standard Trail') }}</span>
                            </div>
                        @endif

                        <div class="mt-3 text-sm text-gray-600 line-clamp-2">
                            {!! Str::markdown($post->body) !!}
                        </div>
                    </div>
                </div>

                <div class="flex flex-col xl:flex-row justify-between items-start xl:items-end mt-auto gap-6">
                    @if ($post->images->count() > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 lg:gap-4 w-full xl:w-[70%]">
                            @foreach ($post->images->take(4) as $index => $image)
                                @if ($index == 3 && $post->images->count() > 4)
                                    <div
                                        class="relative w-full aspect-4/3 sm:aspect-square rounded-xl md:rounded-2xl overflow-hidden cursor-pointer group">
                                        <img src="{{ str_contains($image->image_url, 'http') ? $image->image_url : asset('storage/' . $image->image_url) }}" alt="{{ $post->title }} image"
                                            class="w-full h-full object-cover">
                                        <div
                                            class="absolute inset-0 bg-black/60 flex items-center justify-center text-white font-bold text-2xl group-hover:bg-black/70 transition">
                                            +{{ $post->images->count() - 4 }}
                                        </div>
                                    </div>
                                @else
                                    <img src="{{ str_contains($image->image_url, 'http') ? $image->image_url : asset('storage/' . $image->image_url) }}" alt="{{ $post->title }} image"
                                        class="w-full aspect-4/3 sm:aspect-square rounded-xl md:rounded-2xl object-cover">
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <a href="{{ route('profile.posts.show', $post->id) }}"
                        class="w-full xl:w-auto text-center bg-[#094174] hover:bg-[#105DA3] text-white text-sm font-bold py-2.5 px-6 rounded-full transition shadow-md hover:shadow-lg hover:-translate-y-0.5 shrink-0">
                        View Full Log
                    </a>
                </div>

            </div>
        @empty
            <div class="bg-white rounded-3xl p-6 shadow-sm text-center py-12">
                <p class="text-gray-500">No hiking history yet.</p>
            </div>
        @endforelse

    </div>

    <div class="lg:col-span-1 h-full">
        <div
            class="bg-white rounded-3xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] h-full flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-[#0F172A] text-lg">Top Mountains</h3>
                <span
                    class="bg-gray-100 text-gray-500 text-[10px] uppercase font-bold px-2.5 py-1 rounded-md">Popular</span>
            </div>

            <div class="grow flex flex-col gap-5">
                @if (isset($topMountains) && $topMountains->count() > 0)
                    @foreach ($topMountains as $mountain)
                        <div class="flex items-center justify-between group cursor-pointer"
                            onclick="window.location.href='{{ route('explore.show', $mountain->id) }}'">
                            <div class="flex items-center gap-3">
                                <img src="{{ $mountain->images->first()?->image_url ?? asset('images/default-mountain.jpg') }}"
                                    class="w-11 h-11 rounded-xl object-cover group-hover:ring-2 ring-[#094174] transition-all">
                                <div>
                                    <h4 class="text-sm font-bold text-[#0F172A] group-hover:text-[#094174] transition">
                                        {{ Str::limit($mountain->name, 20) }}</h4>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase mt-0.5">
                                        {{ $mountain->province->name ?? 'Indonesia' }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-yellow-500 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="size-3 w-3 h-3">
                                    <path fill-rule="evenodd"
                                        d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ number_format($mountain->ratings_avg_score ?? 0, 1) }}
                            </span>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-6">
                        <p class="text-sm text-gray-500">No popular mountains available.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
