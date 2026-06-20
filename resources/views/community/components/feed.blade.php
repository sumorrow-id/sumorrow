<div class="flex flex-col gap-6">

    @if(request('tag'))
    <div class="flex items-center gap-2 pb-2 mb-2 border-b border-gray-100">
        <a href="{{ route('community.explore') }}" class="p-1.5 hover:bg-gray-100 rounded-full transition text-gray-500 hover:text-[#1a2b4c]" aria-label="Clear filter">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="font-semibold text-[#1a2b4c] text-base md:text-lg">Showing Top Results for '<span class="text-[#094174]">{{ request('tag') }}</span>'</h2>
    </div>
    @endif

    {{-- ================================================================
         COMPOSER — Post a new feed entry
         ================================================================ --}}

    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>

    <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-4 md:p-5">
        <form id="post-composer-form" method="POST" action="{{ route('community.posts.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- ── Row 1: Avatar + text input + toolbar ── --}}
            <div class="flex items-center gap-3 mb-3">
                <img src="{{ Auth::check() && Auth::user()->avatar_url ? asset(Auth::user()->avatar_url) : asset('images/dummymountain/rinjani.png') }}"
                     class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover shrink-0" alt="Avatar">

                <div class="grow bg-[#F8F9FA] rounded-full flex items-center justify-between px-4 py-2.5 md:py-3">
                    <input id="post-body-input" type="text" name="body"
                           placeholder="What is New, {{ Auth::check() ? Auth::user()->username : 'Hiker' }}?"
                           class="bg-transparent border-none p-0 focus:outline-none focus:ring-0 text-sm md:text-base text-gray-800 placeholder-gray-400 w-full font-medium">

                    {{-- Hidden multi-file input --}}
                    <input type="file" name="images[]" id="post-image-input" accept="image/*" multiple class="hidden">

                    {{-- Hidden GIF URL — populated by JS when a GIF is chosen --}}
                    <input type="hidden" name="gif_url" id="gif-url-hidden" value="">

                    {{-- ── Toolbar icons + popovers anchored here ── --}}
                    <div class="relative flex items-center gap-3 text-gray-400 shrink-0">

                        {{-- 📷 Image picker --}}
                        <button type="button" id="btn-image-picker"
                                class="hover:text-[#094174] transition"
                                onclick="document.getElementById('post-image-input').click()"
                                aria-label="Attach images">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </button>

                        {{-- GIF picker trigger --}}
                        <button type="button" id="btn-gif-toggle"
                                class="hover:text-[#094174] transition font-bold text-[10px] md:text-xs border-2 border-current rounded-sm px-1 py-0.5"
                                style="line-height:1;" aria-label="Insert GIF">GIF</button>

                        {{-- 😊 Emoji picker trigger --}}
                        <button type="button" id="btn-emoji-toggle"
                                class="hover:text-[#094174] transition"
                                aria-label="Insert emoji">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </button>

                        {{-- ── Emoji picker popover ─────────────────────────── --}}
                        <div id="emoji-popover"
                             class="hidden absolute right-0 top-full mt-2 z-[100] shadow-2xl rounded-2xl overflow-hidden border border-gray-200">
                            <emoji-picker id="emoji-picker-el" class="light"></emoji-picker>
                        </div>

                        {{-- ── GIF picker popover ───────────────────────────── --}}
                        <div id="gif-popover"
                             class="hidden absolute right-0 top-full mt-2 z-[100] w-72 bg-white border border-gray-200 rounded-2xl shadow-2xl p-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">GIF Search</p>

                            {{-- Search box --}}
                            <div class="relative mb-3">
                                <input id="gif-search-input" type="text" placeholder="Search GIFs…"
                                       class="w-full bg-[#F8F9FA] rounded-xl pl-9 pr-3 py-2 text-sm text-gray-700 border-none focus:outline-none focus:ring-2 focus:ring-[#094174]/30 placeholder-gray-400">
                                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>

                            {{-- Results — populated by JS (Giphy API) --}}
                            <div id="gif-results-grid" class="grid grid-cols-2 gap-2 max-h-52 overflow-y-auto">
                                {{-- Loading state shown by JS --}}
                            </div>

                            <p class="text-center text-[10px] text-gray-400 mt-2">
                                Powered by <a href="https://giphy.com" target="_blank" rel="noopener" class="underline hover:text-[#094174]">Giphy</a>
                            </p>
                        </div>

                    </div>{{-- /toolbar --}}
                </div>
            </div>

            {{-- ── Image preview grid (multi-upload) ──────────────────── --}}
            <div id="post-image-preview" class="hidden mt-2 mb-3 w-full"></div>

            {{-- ── GIF preview (single selected GIF) ───────────────────── --}}
            <div id="gif-preview-wrap" class="hidden mt-2 mb-3 relative w-full overflow-hidden rounded-xl border border-gray-200 shadow-sm group">
                <img id="gif-preview-img" src="" alt="Selected GIF" class="w-full h-auto object-cover block rounded-xl">
                <button type="button" onclick="clearGifPreview()"
                        class="absolute top-2 right-2 w-7 h-7 bg-gray-900/75 hover:bg-black text-white text-lg leading-none font-bold rounded-full flex items-center justify-center backdrop-blur-sm shadow-md transition-all duration-150 cursor-pointer opacity-0 group-hover:opacity-100"
                        aria-label="Remove GIF">&times;</button>
            </div>

            {{-- ── Validation Errors ─────────────────────────────────── --}}
            @if ($errors->any())
                <div class="mb-3 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600 font-medium space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- ── Category tag pills + Submit ─────────────────────────── --}}
            <div class="flex items-center justify-between gap-4 mt-3 pl-1">
                <div class="flex flex-wrap gap-2 grow">
                    @foreach (['Hiking Stories', 'Tips and Trick', 'Gear & Equipment', 'Safety & Survival'] as $tag)
                        @php
                            $peerClass = match(true) {
                                stripos($tag, 'Hiking') !== false   => 'peer-checked:bg-blue-100 peer-checked:text-blue-600',
                                stripos($tag, 'Tips') !== false     => 'peer-checked:bg-green-100 peer-checked:text-green-600',
                                stripos($tag, 'Gear') !== false     => 'peer-checked:bg-purple-100 peer-checked:text-purple-600',
                                stripos($tag, 'Safety') !== false   => 'peer-checked:bg-red-100 peer-checked:text-red-600',
                                default                             => 'peer-checked:bg-blue-100 peer-checked:text-blue-600',
                            };
                        @endphp
                        <label class="cursor-pointer select-none">
                            <input type="checkbox" name="category_tags[]" value="{{ $tag }}" class="peer hidden">
                            <div class="px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 transition-all border border-transparent peer-checked:border-current {{ $peerClass }}">
                                {{ $tag }}
                            </div>
                        </label>
                    @endforeach
                </div>

                <button id="post-submit-btn" type="submit" disabled
                        class="shrink-0 bg-[#094174] hover:bg-[#105DA3] text-white font-bold text-sm px-5 py-2.5 rounded-xl transition disabled:opacity-40 disabled:cursor-not-allowed">
                    Post
                </button>
            </div>
        </form>


        {{-- ================================================================
             COMPOSER JAVASCRIPT
             ================================================================ --}}
        <script>
        (function () {
            /* ── DOM refs ──────────────────────────────────────────────── */
            var fileInput    = document.getElementById('post-image-input');
            var bodyInput    = document.getElementById('post-body-input');
            var previewWrap  = document.getElementById('post-image-preview');
            var submitBtn    = document.getElementById('post-submit-btn');
            var emojiToggle  = document.getElementById('btn-emoji-toggle');
            var emojiPopover = document.getElementById('emoji-popover');
            var emojiPicker  = document.getElementById('emoji-picker-el');
            var gifToggle    = document.getElementById('btn-gif-toggle');
            var gifPopover   = document.getElementById('gif-popover');
            var gifSearch    = document.getElementById('gif-search-input');

            /*
             * selectedFiles — our private accumulator.
             * The browser replaces fileInput.files on every picker session,
             * so we keep the real list here and sync it back via DataTransfer.
             */
            var selectedFiles = [];
            var objectUrls    = [];

            /* ── Submit guard ──────────────────────────────────────────── */
            function checkPostable() {
                var hasText  = bodyInput.value.trim().length > 0;
                var hasImage = selectedFiles.length > 0;
                submitBtn.disabled = !(hasText || hasImage);
            }

            bodyInput.addEventListener('input', checkPostable);

            /* ── Sync selectedFiles → fileInput.files ──────────────────── */
            function syncToInput() {
                var dt = new DataTransfer();
                selectedFiles.forEach(function (f) { dt.items.add(f); });
                fileInput.files = dt.files;
            }

            /* ── Rebuild the preview grid from selectedFiles ────────────── */
            function rebuildPreview() {
                /* Revoke old blob URLs */
                objectUrls.forEach(function (u) { URL.revokeObjectURL(u); });
                objectUrls = [];
                previewWrap.innerHTML = '';

                if (selectedFiles.length === 0) {
                    previewWrap.classList.add('hidden');
                    checkPostable();
                    return;
                }

                /* Always stack vertically — one image per row */
                previewWrap.className = 'mt-2 mb-3 w-full flex flex-col gap-3';

                selectedFiles.forEach(function (file, idx) {
                    var url = URL.createObjectURL(file);
                    objectUrls.push(url);

                    var cell = document.createElement('div');
                    cell.className = 'relative overflow-hidden group rounded-xl border border-gray-200 shadow-sm';

                    var img = document.createElement('img');
                    img.src = url;
                    img.alt = 'Preview ' + (idx + 1);
                    img.className = 'w-full h-auto object-cover block rounded-xl';

                    /* × button — frosted-glass circle, appears on hover */
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.setAttribute('aria-label', 'Remove image');
                    btn.className = [
                        'absolute top-2 right-2',
                        'w-7 h-7',
                        'bg-gray-900/75 hover:bg-black',
                        'text-white text-lg leading-none font-bold',
                        'rounded-full',
                        'flex items-center justify-center',
                        'backdrop-blur-sm shadow-md',
                        'transition-all duration-150 cursor-pointer',
                        'opacity-0 group-hover:opacity-100',
                    ].join(' ');
                    btn.innerHTML = '&times;';
                    /* capture idx by value */
                    (function (i) {
                        btn.addEventListener('click', function () { removeImage(i); });
                    })(idx);

                    cell.appendChild(img);
                    cell.appendChild(btn);
                    previewWrap.appendChild(cell);
                });

                previewWrap.classList.remove('hidden');
                checkPostable();
            }

            /* ── File picker: MERGE new picks into selectedFiles ──────── */
            fileInput.addEventListener('change', function () {
                var newFiles = Array.from(fileInput.files).filter(function (f) {
                    return f.type.startsWith('image/');
                });

                newFiles.forEach(function (newFile) {
                    /* Deduplicate by name + size + lastModified */
                    var alreadyAdded = selectedFiles.some(function (existing) {
                        return existing.name         === newFile.name
                            && existing.size         === newFile.size
                            && existing.lastModified === newFile.lastModified;
                    });
                    if (!alreadyAdded) selectedFiles.push(newFile);
                });

                syncToInput();   /* keep fileInput.files in sync for form submit */
                rebuildPreview();
            });

            /* ── Remove one thumbnail by index ─────────────────────────── */
            function removeImage(idx) {
                selectedFiles.splice(idx, 1);
                syncToInput();
                rebuildPreview();
            }

            /* ── Full clear ────────────────────────────────────────────── */
            window.clearImagePreview = function () {
                objectUrls.forEach(function (u) { URL.revokeObjectURL(u); });
                objectUrls    = [];
                selectedFiles = [];
                fileInput.value = '';
                previewWrap.innerHTML = '';
                previewWrap.classList.add('hidden');
                checkPostable();
            };

            /* ── Emoji picker ──────────────────────────────────────────── */
            emojiToggle.addEventListener('click', function (e) {
                e.stopPropagation();
                gifPopover.classList.add('hidden');
                emojiPopover.classList.toggle('hidden');
            });

            if (emojiPicker) {
                emojiPicker.addEventListener('emoji-click', function (e) {
                    var emoji = e.detail.unicode;
                    var start = bodyInput.selectionStart;
                    var end   = bodyInput.selectionEnd;
                    var val   = bodyInput.value;
                    bodyInput.value = val.slice(0, start) + emoji + val.slice(end);
                    var pos = start + emoji.length;
                    bodyInput.setSelectionRange(pos, pos);
                    bodyInput.focus();
                    checkPostable();
                });
            }

            /* ── Giphy integration ─────────────────────────────────────── */
            /*
             * ↓ Replace this with your real key from https://developers.giphy.com/
             * The free "sandbox" key below works but is rate-limited.
             */
            var GIPHY_API_KEY = '{{ env('GIPHY_API_KEY') }}';
            var GIPHY_LIMIT   = 12;
            var gifSearchTimer = null;

            var gifGrid       = document.getElementById('gif-results-grid');
            var gifHidden     = document.getElementById('gif-url-hidden');
            var gifPreviewWrap= document.getElementById('gif-preview-wrap');
            var gifPreviewImg = document.getElementById('gif-preview-img');

            /* Show a skeleton grid while loading */
            function showGifSkeleton() {
                var html = '';
                for (var i = 0; i < GIPHY_LIMIT; i++) {
                    html += '<div class="bg-gray-100 rounded-xl h-24 animate-pulse"></div>';
                }
                gifGrid.innerHTML = html;
            }

            /* Render GIF results from Giphy API response */
            function renderGifs(gifs) {
                if (!gifs || gifs.length === 0) {
                    gifGrid.innerHTML = '<p class="col-span-2 text-center text-xs text-gray-400 py-4">No GIFs found.</p>';
                    return;
                }
                gifGrid.innerHTML = gifs.map(function (g) {
                    var thumb = g.images.fixed_height_small.url;
                    var full  = g.images.downsized_medium
                        ? g.images.downsized_medium.url
                        : g.images.original.url;
                    return '<img src="' + thumb + '" alt="' + (g.title || 'GIF') + '"'
                        + ' class="rounded-xl w-full h-24 object-cover cursor-pointer hover:opacity-90 transition-opacity"'
                        + ' loading="lazy"'
                        + ' onclick="selectGif(\'' + full.replace(/'/g, "\\'") + '\', \'' + thumb.replace(/'/g, "\\'") + '\')">';
                }).join('');
            }

            /* Fetch from Giphy (trending or search) */
            function fetchGifs(query) {
                showGifSkeleton();
                var endpoint = query
                    ? 'https://api.giphy.com/v1/gifs/search?api_key=' + GIPHY_API_KEY + '&q=' + encodeURIComponent(query) + '&limit=' + GIPHY_LIMIT + '&rating=g'
                    : 'https://api.giphy.com/v1/gifs/trending?api_key=' + GIPHY_API_KEY + '&limit=' + GIPHY_LIMIT + '&rating=g';

                fetch(endpoint)
                    .then(function (r) { return r.json(); })
                    .then(function (data) { renderGifs(data.data); })
                    .catch(function () {
                        gifGrid.innerHTML = '<p class="col-span-2 text-center text-xs text-red-400 py-4">Failed to load GIFs.</p>';
                    });
            }

            /* GIF button toggle — load trending on first open */
            var gifLoaded = false;
            gifToggle.addEventListener('click', function (e) {
                e.stopPropagation();
                emojiPopover.classList.add('hidden');
                gifPopover.classList.toggle('hidden');
                if (!gifPopover.classList.contains('hidden')) {
                    gifSearch.focus();
                    if (!gifLoaded) {
                        gifLoaded = true;
                        fetchGifs('');   /* trending */
                    }
                }
            });

            /* Debounced search — fires 400 ms after the user stops typing */
            gifSearch.addEventListener('input', function () {
                clearTimeout(gifSearchTimer);
                var q = gifSearch.value.trim();
                gifSearchTimer = setTimeout(function () {
                    fetchGifs(q);   /* empty string → trending */
                }, 400);
            });

            /* Called when a GIF thumbnail is clicked */
            window.selectGif = function (fullUrl, thumbUrl) {
                gifHidden.value    = fullUrl;
                gifPreviewImg.src  = thumbUrl;
                gifPreviewWrap.classList.remove('hidden');
                gifPopover.classList.add('hidden');
                checkPostable();
            };

            /* Clear the selected GIF */
            window.clearGifPreview = function () {
                gifHidden.value   = '';
                gifPreviewImg.src = '';
                gifPreviewWrap.classList.add('hidden');
                checkPostable();
            };

            /* ── Close popovers on outside click ───────────────────────── */
            document.addEventListener('click', function (e) {
                if (!emojiPopover.contains(e.target) && e.target !== emojiToggle) {
                    emojiPopover.classList.add('hidden');
                }
                if (!gifPopover.contains(e.target) && e.target !== gifToggle) {
                    gifPopover.classList.add('hidden');
                }
            });

        })();
        </script>


    </div>


    {{-- ================================================================
         MAIN FEED LOOP
         ================================================================ --}}
    @forelse ($posts as $post)
    <div class="feed-post bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-5 md:p-6 transition hover:shadow-md duration-300" data-tag="{{ $post->tags->first()->keyword ?? '' }}">
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-center gap-3">
                <img src="{{ $post->author->avatar_url ? asset($post->author->avatar_url) : asset('images/dummymountain/rinjani.png') }}" class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover" alt="Avatar">
                <div>
                    <div class="flex items-center gap-1">
                        <span class="font-bold text-[#1a2b4c] text-sm md:text-base">{{ $post->author->username }}</span>
                    </div>
                    <div class="text-xs text-gray-500">{{ '@' . strtolower(str_replace(' ', '', $post->author->username)) }}</div>
                </div>
            </div>
            {{-- Tags Badge(s) --}}
            <div class="flex flex-wrap items-center justify-end gap-2">
                @foreach ($post->tags as $tag)
                    @php
                        $tagConfig = match(true) {
                            stripos($tag->keyword, 'Hiking') !== false   => ['bg' => 'bg-blue-100',   'text' => 'text-blue-500'],
                            stripos($tag->keyword, 'Tips') !== false     => ['bg' => 'bg-green-100',  'text' => 'text-green-500'],
                            stripos($tag->keyword, 'Gear') !== false     => ['bg' => 'bg-purple-100', 'text' => 'text-purple-500'],
                            stripos($tag->keyword, 'Safety') !== false   => ['bg' => 'bg-red-100',    'text' => 'text-red-500'],
                            default                                      => ['bg' => 'bg-blue-100',   'text' => 'text-blue-500'],
                        };
                    @endphp
                    <div class="{{ $tagConfig['bg'] }} {{ $tagConfig['text'] }} text-[10px] md:text-xs font-bold px-3 py-1 rounded-full whitespace-nowrap">{{ $tag->keyword }}</div>
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
                    <img src="{{ asset($image->image_url) }}" alt="Post image" class="w-full max-h-96 object-cover rounded-xl border border-gray-100">
                @endforeach
            </div>
        @endif

        {{-- Post GIF --}}
        @if ($post->gif_url)
            <div class="mb-3">
                <img src="{{ $post->gif_url }}" alt="GIF" class="w-full max-h-96 object-cover rounded-xl border border-gray-100">
            </div>
        @endif

        <div class="text-xs text-gray-400 mb-4 font-medium tracking-wide">
            {{ $post->created_at->format('g:i A · F j Y') }}
        </div>

        <hr class="border-gray-100 mb-4">

        @php
            $isLiked = Auth::check() && $post->likes->contains('id', Auth::id());
            $isSaved = Auth::check() && $post->saves->contains('id', Auth::id());
            $likeCount = $post->likes->count();
        @endphp

        <div class="flex flex-wrap items-center justify-between text-gray-400 gap-y-3">
            <div class="flex items-center gap-4 sm:gap-6">

                {{-- Comment icon → links to post detail page --}}
                <a href="{{ route('community.posts.show', $post->id) }}" class="flex items-center gap-1.5 sm:gap-2 hover:text-[#094174] transition group">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <span class="text-[10px] sm:text-xs md:text-sm font-medium">{{ $post->comments()->count() }}</span>
                </a>

                @auth
                <button
                    type="button"
                    data-post-id="{{ $post->id }}"
                    data-like-url="{{ route('community.posts.like', $post->id) }}"
                    data-liked="{{ $isLiked ? 'true' : 'false' }}"
                    data-count="{{ $likeCount }}"
                    class="like-btn flex items-center gap-1.5 sm:gap-2 hover:text-red-500 transition group {{ $isLiked ? 'text-red-500' : '' }}">
                    <svg class="like-icon w-4 h-4 sm:w-5 sm:h-5" fill="{{ $isLiked ? '#ef4444' : 'none' }}" stroke="{{ $isLiked ? '#ef4444' : 'currentColor' }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    <span class="like-count text-[10px] sm:text-xs md:text-sm font-medium">{{ $likeCount }}</span>
                </button>
                @else
                <button type="button" class="flex items-center gap-1.5 sm:gap-2 hover:text-red-500 transition group" onclick="window.location='{{ route('showLogin') }}'">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    <span class="text-[10px] sm:text-xs md:text-sm font-medium">{{ $likeCount }}</span>
                </button>
                @endauth

            </div>
            <div class="flex items-center gap-3 sm:gap-4">

                @auth
                <button
                    type="button"
                    data-post-id="{{ $post->id }}"
                    data-save-url="{{ route('community.posts.save', $post->id) }}"
                    data-saved="{{ $isSaved ? 'true' : 'false' }}"
                    class="save-btn hover:text-[#094174] transition {{ $isSaved ? 'text-[#094174]' : '' }}">
                    <svg class="save-icon w-4 h-4 sm:w-5 sm:h-5" fill="{{ $isSaved ? '#094174' : 'none' }}" stroke="{{ $isSaved ? '#094174' : 'currentColor' }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                </button>
                @else
                <button type="button" class="hover:text-[#094174] transition" onclick="window.location='{{ route('showLogin') }}'">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                </button>
                @endauth

                {{-- Share button — Web Share API with clipboard fallback --}}
                <button type="button" class="hover:text-[#094174] transition"
                    onclick="(function(url, text) {
                        if (navigator.share) {
                            navigator.share({ title: 'Sumorrow · Forum Post', text: text, url: url }).catch(function(){});
                        } else {
                            navigator.clipboard.writeText(url).then(function() {
                                alert('Link copied to clipboard!');
                            }).catch(function() { prompt('Copy this link:', url); });
                        }
                    })('{{ route('community.posts.show', $post->id) }}', '{{ addslashes(Str::limit($post->body, 100)) }}')">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                </button>

            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-10 text-center text-gray-400">
        <p class="font-medium">No posts yet. Be the first to share something!</p>
    </div>
    @endforelse

    {{-- Pagination --}}
    <div>
        {{ $posts->links() }}
    </div>

</div>

{{-- ================================================================
     ASYNC LIKE & SAVE
     ================================================================ --}}
<script>
(function () {
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value || '';

    // ── LIKE BUTTONS ─────────────────────────────────────────────────
    document.querySelectorAll('.like-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault(); // Stop any default form/button actions

            var url   = btn.dataset.likeUrl;
            var liked = btn.dataset.liked === 'true';
            var icon  = btn.querySelector('.like-icon');
            var count = btn.querySelector('.like-count');

            // Optimistic UI Update
            var newLiked = !liked;
            btn.dataset.liked = newLiked ? 'true' : 'false';
            var curCount = parseInt(count.textContent, 10) || 0;
            var newCount = newLiked ? curCount + 1 : Math.max(0, curCount - 1);
            count.textContent = newCount;

            if (newLiked) {
                icon.setAttribute('fill', '#ef4444');    // red-500
                icon.setAttribute('stroke', '#ef4444');
                btn.classList.add('text-red-500');
            } else {
                icon.setAttribute('fill', 'none');
                icon.setAttribute('stroke', 'currentColor');
                btn.classList.remove('text-red-500');
            }

            fetch(url, {
                method:  'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept':       'application/json',
                    'Content-Type': 'application/json',
                },
            })
            .then(function (res) {
                if (!res.ok) throw new Error('Request failed with status ' + res.status);
                return res.json();
            })
            .then(function (data) {
                // Sync with real server state
                btn.dataset.liked = data.liked ? 'true' : 'false';
                count.textContent = data.count;

                if (data.liked) {
                    icon.setAttribute('fill', '#ef4444');
                    icon.setAttribute('stroke', '#ef4444');
                    btn.classList.add('text-red-500');
                } else {
                    icon.setAttribute('fill', 'none');
                    icon.setAttribute('stroke', 'currentColor');
                    btn.classList.remove('text-red-500');
                }
            })
            .catch(function (error) {
                console.error('Fetch Error:', error);
                
                // Rollback Optimistic UI Update
                btn.dataset.liked = liked ? 'true' : 'false';
                count.textContent = curCount;
                
                if (liked) {
                    icon.setAttribute('fill', '#ef4444');
                    icon.setAttribute('stroke', '#ef4444');
                    btn.classList.add('text-red-500');
                } else {
                    icon.setAttribute('fill', 'none');
                    icon.setAttribute('stroke', 'currentColor');
                    btn.classList.remove('text-red-500');
                }
            });
        });
    });

    // ── SAVE / BOOKMARK BUTTONS ───────────────────────────────────────
    document.querySelectorAll('.save-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            var url   = btn.dataset.saveUrl;
            var saved = btn.dataset.saved === 'true';
            var icon  = btn.querySelector('.save-icon');

            // Optimistic UI Update
            var newSaved = !saved;
            btn.dataset.saved = newSaved ? 'true' : 'false';
            
            if (newSaved) {
                icon.setAttribute('fill', '#094174');
                icon.setAttribute('stroke', '#094174');
                btn.classList.add('text-[#094174]');
            } else {
                icon.setAttribute('fill', 'none');
                icon.setAttribute('stroke', 'currentColor');
                btn.classList.remove('text-[#094174]');
            }

            fetch(url, {
                method:  'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept':       'application/json',
                    'Content-Type': 'application/json',
                },
            })
            .then(function (res) {
                if (!res.ok) throw new Error('Request failed with status ' + res.status);
                return res.json();
            })
            .then(function (data) {
                // Sync with real server state
                btn.dataset.saved = data.saved ? 'true' : 'false';
                if (data.saved) {
                    icon.setAttribute('fill', '#094174');
                    icon.setAttribute('stroke', '#094174');
                    btn.classList.add('text-[#094174]');
                } else {
                    icon.setAttribute('fill', 'none');
                    icon.setAttribute('stroke', 'currentColor');
                    btn.classList.remove('text-[#094174]');
                }
            })
            .catch(function (error) {
                console.error('Fetch Error:', error);
                
                // Rollback Optimistic UI Update
                btn.dataset.saved = saved ? 'true' : 'false';
                if (saved) {
                    icon.setAttribute('fill', '#094174');
                    icon.setAttribute('stroke', '#094174');
                    btn.classList.add('text-[#094174]');
                } else {
                    icon.setAttribute('fill', 'none');
                    icon.setAttribute('stroke', 'currentColor');
                    btn.classList.remove('text-[#094174]');
                }
            });
        });
    });
})();
</script>
