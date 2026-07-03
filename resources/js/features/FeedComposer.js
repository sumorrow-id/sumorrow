export class FeedComposer {
    /**
     * Initializes the FeedComposer class.
     * @param {HTMLFormElement} form
     */
    constructor(form) {
        if (!form) return;

        this.form = form;

        /* ── DOM refs ──────────────────────────────────────────────── */
        this.fileInput = document.getElementById('post-image-input');
        this.bodyInput = document.getElementById('post-body-input');
        this.previewWrap = document.getElementById('post-image-preview');
        this.submitBtn = document.getElementById('post-submit-btn');
        this.imagePickerBtn = document.getElementById('btn-image-picker');
        this.emojiToggle = document.getElementById('btn-emoji-toggle');
        this.emojiPopover = document.getElementById('emoji-popover');
        this.emojiPicker = document.getElementById('emoji-picker-el');
        this.gifToggle = document.getElementById('btn-gif-toggle');
        this.gifPopover = document.getElementById('gif-popover');
        this.gifSearch = document.getElementById('gif-search-input');
        this.gifGrid = document.getElementById('gif-results-grid');
        this.gifHidden = document.getElementById('gif-url-hidden');
        this.gifPreviewWrap = document.getElementById('gif-preview-wrap');
        this.gifPreviewImg = document.getElementById('gif-preview-img');
        this.clearGifBtn = document.getElementById('btn-clear-gif');
        this.categoryInputs = form.querySelectorAll('input[name="category_tags[]"]');

        /*
         * selectedFiles — our private accumulator.
         * The browser replaces fileInput.files on every picker session,
         * so we keep the real list here and sync it back via DataTransfer.
         */
        this.selectedFiles = [];
        this.objectUrls = [];

        /* ── Giphy integration state ──────────────────────────────── */
        this.GIPHY_API_KEY = this.form.dataset.giphyKey;
        this.GIPHY_LIMIT = 12;
        this.gifSearchTimer = null;
        this.gifLoaded = false;

        this.init();
    }

    init() {
        this.bodyInput.addEventListener('input', () => this.checkPostable());

        /* Category pills gate the submit button too */
        this.categoryInputs.forEach((input) => {
            input.addEventListener('change', () => this.checkPostable());
        });

        /* ── Image picker button ───────────────────────────────────── */
        if (this.imagePickerBtn) {
            this.imagePickerBtn.addEventListener('click', () => this.fileInput.click());
        }

        /* ── File picker: MERGE new picks into selectedFiles ──────── */
        this.fileInput.addEventListener('change', () => {
            const newFiles = Array.from(this.fileInput.files).filter((f) => f.type.startsWith('image/'));

            newFiles.forEach((newFile) => {
                /* Deduplicate by name + size + lastModified */
                const alreadyAdded = this.selectedFiles.some((existing) => {
                    return existing.name === newFile.name &&
                        existing.size === newFile.size &&
                        existing.lastModified === newFile.lastModified;
                });
                if (!alreadyAdded) this.selectedFiles.push(newFile);
            });

            this.syncToInput(); /* keep fileInput.files in sync for form submit */
            this.rebuildPreview();
        });

        /* ── Emoji picker ──────────────────────────────────────────── */
        this.emojiToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            this.gifPopover.classList.add('hidden');
            this.emojiPopover.classList.toggle('hidden');
        });

        if (this.emojiPicker) {
            this.emojiPicker.addEventListener('emoji-click', (e) => {
                const emoji = e.detail.unicode;
                const start = this.bodyInput.selectionStart;
                const end = this.bodyInput.selectionEnd;
                const val = this.bodyInput.value;
                this.bodyInput.value = val.slice(0, start) + emoji + val.slice(end);
                const pos = start + emoji.length;
                this.bodyInput.setSelectionRange(pos, pos);
                this.bodyInput.focus();
                this.checkPostable();
            });
        }

        /* ── GIF button toggle — load trending on first open ───────── */
        this.gifToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            this.emojiPopover.classList.add('hidden');
            this.gifPopover.classList.toggle('hidden');
            if (!this.gifPopover.classList.contains('hidden')) {
                this.gifSearch.focus();
                if (!this.gifLoaded) {
                    this.gifLoaded = true;
                    this.fetchGifs(''); /* trending */
                }
            }
        });

        /* Debounced search — fires 400 ms after the user stops typing */
        this.gifSearch.addEventListener('input', () => {
            clearTimeout(this.gifSearchTimer);
            const q = this.gifSearch.value.trim();
            this.gifSearchTimer = setTimeout(() => {
                this.fetchGifs(q); /* empty string → trending */
            }, 400);
        });

        /* Delegated listener for GIF result thumbnails */
        this.gifGrid.addEventListener('click', (e) => {
            const img = e.target.closest('img[data-full]');
            if (img) {
                this.selectGif(img.dataset.full, img.dataset.thumb);
            }
        });

        /* Clear the selected GIF */
        if (this.clearGifBtn) {
            this.clearGifBtn.addEventListener('click', () => this.clearGifPreview());
        }

        /* ── Close popovers on outside click ───────────────────────── */
        document.addEventListener('click', (e) => {
            if (!this.emojiPopover.contains(e.target) && e.target !== this.emojiToggle) {
                this.emojiPopover.classList.add('hidden');
            }
            if (!this.gifPopover.contains(e.target) && e.target !== this.gifToggle) {
                this.gifPopover.classList.add('hidden');
            }
        });

        /*
         * Evaluate once on load: after a failed-validation redirect the
         * browser repopulates body/categories from old input without firing
         * any events, which used to leave the button stuck on disabled.
         */
        this.checkPostable();
    }

    /* ── Submit guard ──────────────────────────────────────────────── */
    checkPostable() {
        const hasText = this.bodyInput.value.trim().length > 0;
        const hasImage = this.selectedFiles.length > 0;
        const hasGif = this.gifHidden.value.trim().length > 0;
        const hasCategory = Array.from(this.categoryInputs).some((input) => input.checked);
        this.submitBtn.disabled = !((hasText || hasImage || hasGif) && hasCategory);
    }

    /* ── Sync selectedFiles → fileInput.files ────────────────────────── */
    syncToInput() {
        const dt = new DataTransfer();
        this.selectedFiles.forEach((f) => dt.items.add(f));
        this.fileInput.files = dt.files;
    }

    /* ── Rebuild the preview grid from selectedFiles ──────────────────── */
    rebuildPreview() {
        /* Revoke old blob URLs */
        this.objectUrls.forEach((u) => URL.revokeObjectURL(u));
        this.objectUrls = [];
        this.previewWrap.innerHTML = '';

        if (this.selectedFiles.length === 0) {
            this.previewWrap.classList.add('hidden');
            this.checkPostable();
            return;
        }

        /* Always stack vertically — one image per row */
        this.previewWrap.className = 'mt-2 mb-3 w-full flex flex-col gap-3';

        this.selectedFiles.forEach((file, idx) => {
            const url = URL.createObjectURL(file);
            this.objectUrls.push(url);

            const cell = document.createElement('div');
            cell.className = 'relative overflow-hidden group rounded-xl border border-gray-200 shadow-sm';

            const img = document.createElement('img');
            img.src = url;
            img.alt = 'Preview ' + (idx + 1);
            img.className = 'w-full h-auto object-cover block rounded-xl';

            /* × button — frosted-glass circle, appears on hover */
            const btn = document.createElement('button');
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
            btn.addEventListener('click', () => this.removeImage(idx));

            cell.appendChild(img);
            cell.appendChild(btn);
            this.previewWrap.appendChild(cell);
        });

        this.previewWrap.classList.remove('hidden');
        this.checkPostable();
    }

    /* ── Remove one thumbnail by index ───────────────────────────────── */
    removeImage(idx) {
        this.selectedFiles.splice(idx, 1);
        this.syncToInput();
        this.rebuildPreview();
    }

    /* ── Giphy: show a skeleton grid while loading ───────────────────── */
    showGifSkeleton() {
        let html = '';
        for (let i = 0; i < this.GIPHY_LIMIT; i++) {
            html += '<div class="bg-gray-100 rounded-xl h-24 animate-pulse"></div>';
        }
        this.gifGrid.innerHTML = html;
    }

    /* ── Giphy: render GIF results from Giphy API response ───────────── */
    renderGifs(gifs) {
        if (!gifs || gifs.length === 0) {
            this.gifGrid.innerHTML =
                '<p class="col-span-2 text-center text-xs text-gray-400 py-4">No GIFs found.</p>';
            return;
        }
        this.gifGrid.innerHTML = gifs.map((g) => {
            const thumb = g.images.fixed_height_small.url;
            const full = g.images.downsized_medium ?
                g.images.downsized_medium.url :
                g.images.original.url;
            return '<img src="' + thumb + '" alt="' + (g.title || 'GIF') + '"' +
                ' class="rounded-xl w-full h-24 object-cover cursor-pointer hover:opacity-90 transition-opacity"' +
                ' loading="lazy"' +
                ' data-full="' + full.replace(/"/g, '&quot;') + '"' +
                ' data-thumb="' + thumb.replace(/"/g, '&quot;') + '">';
        }).join('');
    }

    /* ── Giphy: fetch (trending or search) ────────────────────────────── */
    fetchGifs(query) {
        this.showGifSkeleton();
        const endpoint = query ?
            'https://api.giphy.com/v1/gifs/search?api_key=' + this.GIPHY_API_KEY + '&q=' + encodeURIComponent(
                query) + '&limit=' + this.GIPHY_LIMIT + '&rating=g' :
            'https://api.giphy.com/v1/gifs/trending?api_key=' + this.GIPHY_API_KEY + '&limit=' + this.GIPHY_LIMIT +
                '&rating=g';

        fetch(endpoint)
            .then((r) => r.json())
            .then((data) => this.renderGifs(data.data))
            .catch(() => {
                this.gifGrid.innerHTML =
                    '<p class="col-span-2 text-center text-xs text-red-400 py-4">Failed to load GIFs.</p>';
            });
    }

    /* ── Called when a GIF thumbnail is clicked ───────────────────────── */
    selectGif(fullUrl, thumbUrl) {
        this.gifHidden.value = fullUrl;
        this.gifPreviewImg.src = thumbUrl;
        this.gifPreviewWrap.classList.remove('hidden');
        this.gifPopover.classList.add('hidden');
        this.checkPostable();
    }

    /* ── Clear the selected GIF ───────────────────────────────────────── */
    clearGifPreview() {
        this.gifHidden.value = '';
        this.gifPreviewImg.src = '';
        this.gifPreviewWrap.classList.add('hidden');
        this.checkPostable();
    }
}
