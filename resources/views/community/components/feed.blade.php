{{-- When included with a $feedCommunity, the composer posts into that
     community (members only) instead of the global forum feed. --}}
<div class="flex flex-col gap-6">

    @if (request('tag'))
        <div class="flex items-center gap-2 pb-2 mb-2 border-b border-gray-100">
            <a href="{{ route('community.explore') }}"
                class="p-1.5 hover:bg-gray-100 rounded-full transition text-gray-500 hover:text-[#1a2b4c]"
                aria-label="{{ __('community.clear_filter_aria') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="font-semibold text-[#1a2b4c] text-base md:text-lg">{{ __('community.showing_results_for') }} '<span
                    class="text-[#094174]">{{ request('tag') }}</span>'</h2>
        </div>
    @endif

    {{-- ================================================================
         COMPOSER — Post a new feed entry
         ================================================================ --}}

    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>

    @if (isset($feedCommunity) && ! ($canPostInCommunity ?? false))
        <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-4 md:p-5 flex items-center gap-3 text-sm text-gray-500">
            <svg class="w-5 h-5 shrink-0 text-[#094174]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ __('community.join_to_post') }}
        </div>
    @else
    <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-4 md:p-5">
        <form id="post-composer-form" method="POST" action="{{ route('community.posts.store') }}"
            enctype="multipart/form-data" data-giphy-key="{{ env('GIPHY_API_KEY') }}">
            @csrf
            @isset($feedCommunity)
                <input type="hidden" name="community_id" value="{{ $feedCommunity->id }}">
            @endisset

            {{-- ── Row 1: Avatar + text input + toolbar ── --}}
            <div class="flex items-center gap-3 mb-3">
                <img src="{{ Auth::check() && Auth::user()->avatar_url ? asset(Auth::user()->avatar_url) : asset('images/dummymountain/rinjani.png') }}"
                    class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover shrink-0" alt="{{ __('community.avatar_alt') }}">

                <div class="grow bg-[#F8F9FA] rounded-full flex items-center justify-between px-4 py-2.5 md:py-3">
                    <input id="post-body-input" type="text" name="body" value="{{ old('body') }}"
                        placeholder="{{ __('community.whats_new_placeholder', ['username' => Auth::check() ? Auth::user()->username : __('community.hiker_fallback')]) }}"
                        class="bg-transparent border-none p-0 focus:outline-none focus:ring-0 text-sm md:text-base text-gray-800 placeholder-gray-400 w-full font-medium">

                    {{-- Hidden multi-file input --}}
                    <input type="file" name="images[]" id="post-image-input" accept="image/*" multiple
                        class="hidden">

                    {{-- Hidden GIF URL — populated by JS when a GIF is chosen --}}
                    <input type="hidden" name="gif_url" id="gif-url-hidden" value="{{ old('gif_url') }}">

                    {{-- ── Toolbar icons + popovers anchored here ── --}}
                    <div class="relative flex items-center gap-2 md:gap-3 pl-2 text-gray-400 shrink-0">

                        {{-- 📷 Image picker --}}
                        <button type="button" id="btn-image-picker" class="hover:text-[#094174] transition"
                            aria-label="{{ __('community.attach_images_aria') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </button>

                        {{-- GIF picker trigger --}}
                        <button type="button" id="btn-gif-toggle"
                            class="hover:text-[#094174] transition font-bold text-[10px] md:text-xs border-2 border-current rounded-sm px-1 py-0.5"
                            style="line-height:1;" aria-label="{{ __('community.insert_gif_aria') }}">GIF</button>

                        {{-- 😊 Emoji picker trigger --}}
                        <button type="button" id="btn-emoji-toggle" class="hover:text-[#094174] transition"
                            aria-label="{{ __('community.insert_emoji_aria') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>

                        {{-- ── Emoji picker popover ─────────────────────────── --}}
                        {{-- Fixed + viewport-centered on mobile so the wide picker never clips off-screen --}}
                        <div id="emoji-popover"
                            class="hidden fixed sm:absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 sm:left-auto sm:right-0 sm:top-full sm:translate-x-0 sm:translate-y-0 sm:mt-2 max-w-[calc(100vw-2rem)] z-[100] shadow-2xl rounded-2xl overflow-hidden border border-gray-200">
                            <emoji-picker id="emoji-picker-el" class="light"></emoji-picker>
                        </div>

                        {{-- ── GIF picker popover ───────────────────────────── --}}
                        <div id="gif-popover"
                            class="hidden absolute right-0 top-full mt-2 z-[100] w-72 max-w-[calc(100vw-3rem)] bg-white border border-gray-200 rounded-2xl shadow-2xl p-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">{{ __('community.gif_search_label') }}</p>

                            {{-- Search box --}}
                            <div class="relative mb-3">
                                <input id="gif-search-input" type="text" placeholder="{{ __('community.search_gifs_placeholder') }}"
                                    class="w-full bg-[#F8F9FA] rounded-xl pl-9 pr-3 py-2 text-sm text-gray-700 border-none focus:outline-none focus:ring-2 focus:ring-[#094174]/30 placeholder-gray-400">
                                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>

                            {{-- Results — populated by JS (Giphy API) --}}
                            <div id="gif-results-grid" class="grid grid-cols-2 gap-2 max-h-52 overflow-y-auto">
                                {{-- Loading state shown by JS --}}
                            </div>

                            <p class="text-center text-[10px] text-gray-400 mt-2">
                                {{ __('community.powered_by') }} <a href="https://giphy.com" target="_blank" rel="noopener"
                                    class="underline hover:text-[#094174]">Giphy</a>
                            </p>
                        </div>

                    </div>{{-- /toolbar --}}
                </div>
            </div>

            {{-- ── Image preview grid (multi-upload) ──────────────────── --}}
            <div id="post-image-preview" class="hidden mt-2 mb-3 w-full"></div>

            {{-- ── GIF preview (single selected GIF) ───────────────────── --}}
            <div id="gif-preview-wrap"
                class="hidden mt-2 mb-3 relative w-full overflow-hidden rounded-xl border border-gray-200 shadow-sm group">
                <img id="gif-preview-img" src="" alt="{{ __('community.selected_gif_alt') }}"
                    class="w-full h-auto object-cover block rounded-xl">
                <button type="button" id="btn-clear-gif"
                    class="absolute top-2 right-2 w-7 h-7 bg-gray-900/75 hover:bg-black text-white text-lg leading-none font-bold rounded-full flex items-center justify-center backdrop-blur-sm shadow-md transition-all duration-150 cursor-pointer opacity-0 group-hover:opacity-100"
                    aria-label="{{ __('community.remove_gif_aria') }}">&times;</button>
            </div>

            {{-- ── Validation Errors ─────────────────────────────────── --}}
            @if ($errors->any())
                <div
                    class="mb-3 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600 font-medium space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- ── Category tag pills + Submit ─────────────────────────── --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 mt-3 pl-1">
                <div class="flex flex-wrap gap-2 grow">
                    @foreach (['Hiking Stories', 'Tips and Trick', 'Gear & Equipment', 'Safety & Survival'] as $tag)
                        @php
                            $peerClass = match (true) {
                                stripos($tag, 'Hiking') !== false
                                    => 'peer-checked:bg-blue-100 peer-checked:text-blue-600',
                                stripos($tag, 'Tips') !== false
                                    => 'peer-checked:bg-green-100 peer-checked:text-green-600',
                                stripos($tag, 'Gear') !== false
                                    => 'peer-checked:bg-purple-100 peer-checked:text-purple-600',
                                stripos($tag, 'Safety') !== false
                                    => 'peer-checked:bg-red-100 peer-checked:text-red-600',
                                default => 'peer-checked:bg-blue-100 peer-checked:text-blue-600',
                            };
                        @endphp
                        <label class="cursor-pointer select-none">
                            <input type="checkbox" name="category_tags[]" value="{{ $tag }}"
                                @checked(in_array($tag, old('category_tags', [])))
                                class="peer hidden">
                            <div
                                class="px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 transition-all border border-transparent peer-checked:border-current {{ $peerClass }}">
                                {{ $tag }}
                            </div>
                        </label>
                    @endforeach
                </div>

                <button id="post-submit-btn" type="submit" disabled
                    class="shrink-0 self-end sm:self-auto bg-[#094174] hover:bg-[#105DA3] text-white font-bold text-sm px-5 py-2.5 rounded-xl transition disabled:opacity-40 disabled:cursor-not-allowed">
                    {{ __('community.post_button') }}
                </button>
            </div>
        </form>


    </div>
    @endif


    {{-- ================================================================
         MAIN FEED LOOP
         ================================================================ --}}
    @forelse ($posts as $post)
        <div class="feed-post bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-5 md:p-6 transition hover:shadow-md duration-300"
            data-tag="{{ $post->tags->first()->keyword ?? '' }}">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-3">
                    <img src="{{ $post->author->avatar_url ? asset($post->author->avatar_url) : asset('images/dummymountain/rinjani.png') }}"
                        class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover" alt="{{ __('community.avatar_alt') }}">
                    <div>
                        <div class="flex items-center gap-1">
                            <span
                                class="font-bold text-[#1a2b4c] text-sm md:text-base">{{ $post->author->username }}</span>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ '@' . strtolower(str_replace(' ', '', $post->author->username)) }}</div>
                    </div>
                </div>
                {{-- Tags Badge(s) --}}
                <div class="flex flex-wrap items-center justify-end gap-2">
                    @foreach ($post->tags as $tag)
                        @php
                            $tagConfig = match (true) {
                                stripos($tag->keyword, 'Hiking') !== false => [
                                    'bg' => 'bg-blue-100',
                                    'text' => 'text-blue-500',
                                ],
                                stripos($tag->keyword, 'Tips') !== false => [
                                    'bg' => 'bg-green-100',
                                    'text' => 'text-green-500',
                                ],
                                stripos($tag->keyword, 'Gear') !== false => [
                                    'bg' => 'bg-purple-100',
                                    'text' => 'text-purple-500',
                                ],
                                stripos($tag->keyword, 'Safety') !== false => [
                                    'bg' => 'bg-red-100',
                                    'text' => 'text-red-500',
                                ],
                                default => ['bg' => 'bg-blue-100', 'text' => 'text-blue-500'],
                            };
                        @endphp
                        <div
                            class="{{ $tagConfig['bg'] }} {{ $tagConfig['text'] }} text-[10px] md:text-xs font-bold px-3 py-1 rounded-full whitespace-nowrap">
                            {{ $tag->keyword }}</div>
                    @endforeach
                </div>
            </div>

            <p class="text-[#1a2b4c] text-sm md:text-base mb-2">
                {{ $post->body }}
            </p>

            {{-- Post Images --}}
            @if ($post->images->isNotEmpty())
                <div class="mb-3 flex flex-col gap-3">
                    @foreach ($post->images as $image)
                        <img src="{{ asset($image->image_url) }}" alt="{{ __('community.post_image_alt') }}"
                            class="w-full max-h-96 object-cover rounded-xl border border-gray-100">
                    @endforeach
                </div>
            @endif

            {{-- Post GIF --}}
            @if ($post->gif_url)
                <div class="mb-3">
                    <img src="{{ $post->gif_url }}" alt="{{ __('community.gif_alt') }}"
                        class="w-full max-h-96 object-cover rounded-xl border border-gray-100">
                </div>
            @endif

            <div class="text-xs text-gray-400 mb-4 font-medium tracking-wide">
                {{ $post->created_at->translatedFormat('g:i A · F j Y') }}
            </div>

            <hr class="border-gray-100 mb-4">

            @php
                $isLiked = Auth::check() && $post->likes->contains('id', Auth::id());
                $likeCount = $post->likes->count();
                // Inside a community, only members may like posts.
                $viewerCanInteract = Auth::check() && (! isset($feedCommunity) || ($canPostInCommunity ?? false));
            @endphp

            <div class="flex flex-wrap items-center justify-between text-gray-400 gap-y-3">
                <div class="flex items-center gap-4 sm:gap-6">

                    {{-- Comment icon → links to post detail page --}}
                    <a href="{{ route('community.posts.show', $post->id) }}"
                        class="flex items-center gap-1.5 sm:gap-2 hover:text-[#094174] transition group">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>
                        <span
                            class="text-[10px] sm:text-xs md:text-sm font-medium">{{ $post->comments()->count() }}</span>
                    </a>

                    @if ($viewerCanInteract)
                        <button type="button" data-post-id="{{ $post->id }}"
                            data-like-url="{{ route('community.posts.like', $post->id) }}"
                            data-liked="{{ $isLiked ? 'true' : 'false' }}" data-count="{{ $likeCount }}"
                            class="like-btn flex items-center gap-1.5 sm:gap-2 hover:text-red-500 transition group {{ $isLiked ? 'text-red-500' : '' }}">
                            <svg class="like-icon w-4 h-4 sm:w-5 sm:h-5" fill="{{ $isLiked ? '#ef4444' : 'none' }}"
                                stroke="{{ $isLiked ? '#ef4444' : 'currentColor' }}" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                </path>
                            </svg>
                            <span
                                class="like-count text-[10px] sm:text-xs md:text-sm font-medium">{{ $likeCount }}</span>
                        </button>
                    @elseif (Auth::check())
                        {{-- Logged in but not a member of this community: read-only count --}}
                        <span class="flex items-center gap-1.5 sm:gap-2 opacity-60 cursor-not-allowed"
                            title="{{ __('community.join_to_interact') }}">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                </path>
                            </svg>
                            <span class="text-[10px] sm:text-xs md:text-sm font-medium">{{ $likeCount }}</span>
                        </span>
                    @else
                        <button type="button"
                            class="flex items-center gap-1.5 sm:gap-2 hover:text-red-500 transition group"
                            onclick="window.location='{{ route('showLogin') }}'">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                </path>
                            </svg>
                            <span class="text-[10px] sm:text-xs md:text-sm font-medium">{{ $likeCount }}</span>
                        </button>
                    @endif

                </div>
                <div class="flex items-center gap-3 sm:gap-4">

                    {{-- Share button — Web Share API with clipboard fallback --}}
                    <button type="button" class="hover:text-[#094174] transition"
                        onclick="(function(url, text) {
                        if (navigator.share) {
                            navigator.share({ title: {{ Illuminate\Support\Js::from(__('community.share_title')) }}, text: text, url: url }).catch(function(){});
                        } else {
                            navigator.clipboard.writeText(url).then(function() {
                                alert({{ Illuminate\Support\Js::from(__('community.link_copied')) }});
                            }).catch(function() { prompt({{ Illuminate\Support\Js::from(__('community.copy_link_prompt')) }}, url); });
                        }
                    })('{{ route('community.posts.show', $post->id) }}', '{{ addslashes(Str::limit($post->body, 100)) }}')">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z">
                            </path>
                        </svg>
                    </button>

                    {{-- Delete button — only for the post author --}}
                    @auth
                        @if (Auth::id() === $post->author_id)
                            <form method="POST" action="{{ route('community.posts.destroy', $post->id) }}"
                                onsubmit="return confirm({{ Illuminate\Support\Js::from(__('community.confirm_delete_post')) }})">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="hover:text-red-500 transition"
                                    title="{{ __('community.delete_post') }}"
                                    aria-label="{{ __('community.delete_post') }}">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    @endauth

                </div>
            </div>
        </div>
    @empty
        <div
            class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-10 text-center text-gray-400">
            <p class="font-medium">{{ __('community.no_posts_yet') }}</p>
        </div>
    @endforelse

    {{-- Pagination --}}
    <div>
        {{ $posts->links() }}
    </div>

</div>
