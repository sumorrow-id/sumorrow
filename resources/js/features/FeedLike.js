export class FeedLike {
    /**
     * Initializes the FeedLike class.
     */
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document
            .querySelector('input[name="_token"]')?.value || '';

        this.init();
    }

    init() {
        var csrfToken = this.csrfToken;

        // ── LIKE BUTTONS ─────────────────────────────────────────────────
        document.querySelectorAll('.like-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault(); // Stop any default form/button actions

                var url = btn.dataset.likeUrl;
                var liked = btn.dataset.liked === 'true';
                var icon = btn.querySelector('.like-icon');
                var count = btn.querySelector('.like-count');

                // Optimistic UI Update
                var newLiked = !liked;
                btn.dataset.liked = newLiked ? 'true' : 'false';
                var curCount = parseInt(count.textContent, 10) || 0;
                var newCount = newLiked ? curCount + 1 : Math.max(0, curCount - 1);
                count.textContent = newCount;

                if (newLiked) {
                    icon.setAttribute('fill', '#ef4444'); // red-500
                    icon.setAttribute('stroke', '#ef4444');
                    btn.classList.add('text-red-500');
                } else {
                    icon.setAttribute('fill', 'none');
                    icon.setAttribute('stroke', 'currentColor');
                    btn.classList.remove('text-red-500');
                }

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(function(res) {
                        if (!res.ok) throw new Error('Request failed with status ' + res
                        .status);
                        return res.json();
                    })
                    .then(function(data) {
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
                    .catch(function(error) {
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

        // ── SAVE / BOOKMARK BUTTONS ──────────────────────────────────────
        document.querySelectorAll('.save-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                var url = btn.dataset.saveUrl;
                var saved = btn.dataset.saved === 'true';
                var icon = btn.querySelector('.save-icon');

                function paint(isSaved) {
                    btn.dataset.saved = isSaved ? 'true' : 'false';
                    if (isSaved) {
                        icon.setAttribute('fill', '#094174');
                        icon.setAttribute('stroke', '#094174');
                        btn.classList.add('text-[#094174]');
                    } else {
                        icon.setAttribute('fill', 'none');
                        icon.setAttribute('stroke', 'currentColor');
                        btn.classList.remove('text-[#094174]');
                    }
                }

                // Optimistic UI update, then sync with server state
                paint(!saved);

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(function(res) {
                        if (!res.ok) throw new Error('Request failed with status ' + res.status);
                        return res.json();
                    })
                    .then(function(data) {
                        paint(data.saved);
                    })
                    .catch(function(error) {
                        console.error('Fetch Error:', error);
                        paint(saved); // rollback
                    });
            });
        });

    }
}
