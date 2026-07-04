<div class="space-y-6 mt-6">
    <div class="flex justify-between items-end mb-6">
        <h2 class="text-2xl font-bold text-[#0F172A]">{{ __('profile.hiking_history') }}</h2>
        <div class="flex gap-4 items-center">
            <a href="{{ route('profile.posts.index') }}" class="text-sm font-bold text-[#2A5C9A] hover:underline">{{ __('profile.view_all_activities') }}</a>
            <a href="{{ route('profile.posts.create') }}"
                class="bg-[#094174] hover:bg-[#105DA3] text-white text-sm font-bold py-2 px-4 rounded-full transition shadow-md hover:shadow-lg hover:-translate-y-0.5 whitespace-nowrap">{{ __('profile.new_activity') }}</a>
        </div>
    </div>

    @forelse($hikingPosts as $post)
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
                                {{ __('profile.expedition_days_badge', ['days' => $post->duration_days]) }}
                            </span>
                        @endif
                    </div>

                    <p class="text-[13px] text-gray-500 mt-1">
                        {{ $post->mountain?->province?->name ?? 'Indonesia' }} •
                        {{ $post->climbing_date ? $post->climbing_date->translatedFormat('M d, Y') : $post->created_at->translatedFormat('M d, Y') }}
                    </p>

                    @if ($post->route_name || isset($post->mountain->route))
                        <div class="flex items-center gap-2 mt-3 text-sm text-gray-600">
                            <img src="{{ asset('images/profile/route.png') }}" alt="{{ __('profile.route_icon_alt') }}"
                                class="w-4 h-4 object-contain">
                            <span>{{ __('profile.route_label') }}
                                {{ $post->route_name ?? ($post->mountain->route ?? __('profile.standard_trail')) }}</span>
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
                                    <img src="{{ str_contains($image->image_url, 'http') ? $image->image_url : asset('storage/' . $image->image_url) }}" alt="{{ $post->title }} {{ __('profile.image_alt_suffix') }}"
                                        class="w-full h-full object-cover">
                                    <div
                                        class="absolute inset-0 bg-black/60 flex items-center justify-center text-white font-bold text-2xl group-hover:bg-black/70 transition">
                                        +{{ $post->images->count() - 4 }}
                                    </div>
                                </div>
                            @else
                                <img src="{{ str_contains($image->image_url, 'http') ? $image->image_url : asset('storage/' . $image->image_url) }}" alt="{{ $post->title }} {{ __('profile.image_alt_suffix') }}"
                                    class="w-full aspect-4/3 sm:aspect-square rounded-xl md:rounded-2xl object-cover">
                            @endif
                        @endforeach
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto shrink-0">
                    <form method="POST" action="{{ route('community.posts.destroy', $post->id) }}"
                        class="w-full sm:w-auto confirm-submit-form"
                        data-confirm-title="{{ __('community.delete_post') }}"
                        data-confirm-message="{{ __('community.confirm_delete_post') }}"
                        data-confirm-label="{{ __('common.delete') }}"
                        data-confirm-variant="danger">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full text-center border border-red-200 text-red-500 hover:bg-red-50 text-sm font-bold py-2.5 px-6 rounded-full transition">
                            {{ __('common.delete') }}
                        </button>
                    </form>

                    <a href="{{ route('profile.posts.show', $post->id) }}"
                        class="w-full sm:w-auto text-center bg-[#094174] hover:bg-[#105DA3] text-white text-sm font-bold py-2.5 px-6 rounded-full transition shadow-md hover:shadow-lg hover:-translate-y-0.5">
                        {{ __('profile.view_full_log') }}
                    </a>
                </div>
            </div>

        </div>
    @empty
        <div class="bg-white rounded-3xl p-6 shadow-sm text-center py-12">
            <p class="text-gray-500">{{ __('profile.no_hiking_history') }}</p>
        </div>
    @endforelse

</div>
