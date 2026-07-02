export class FollowToggle {
    /**
     * Initializes the FollowToggle class.
     * @param {NodeListOf<HTMLButtonElement>} buttons
     */
    constructor(buttons) {
        if (!buttons || !buttons.length) return;

        this.buttons = buttons;

        this.init();
    }

    init() {
        this.buttons.forEach((btn) => {
            btn.addEventListener('click', () => this.toggleFollow(btn));
        });
    }

    async toggleFollow(btn) {
        const url = btn.dataset.url;
        const isFollowing = btn.dataset.following === 'true';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value || '';

        // Optimistic UI Update
        const newFollowing = !isFollowing;
        btn.dataset.following = newFollowing ? 'true' : 'false';

        if (newFollowing) {
            btn.textContent = 'Following';
            btn.classList.remove('bg-[#094174]', 'text-white', 'hover:bg-[#105DA3]');
            btn.classList.add('bg-white', 'text-[#094174]', 'hover:bg-gray-50');
        } else {
            btn.textContent = 'Follow';
            btn.classList.remove('bg-white', 'text-[#094174]', 'hover:bg-gray-50');
            btn.classList.add('bg-[#094174]', 'text-white', 'hover:bg-[#105DA3]');
        }

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            });

            if (!res.ok) {
                const errorText = await res.text();
                console.error('Laravel Error:', errorText);
                throw new Error('Network response was not ok');
            }

            const data = await res.json();

            // Sync with server
            btn.dataset.following = data.is_following ? 'true' : 'false';
            if (data.is_following) {
                btn.textContent = 'Following';
                btn.classList.remove('bg-[#094174]', 'text-white', 'hover:bg-[#105DA3]');
                btn.classList.add('bg-white', 'text-[#094174]', 'hover:bg-gray-50');
            } else {
                btn.textContent = 'Follow';
                btn.classList.remove('bg-white', 'text-[#094174]', 'hover:bg-gray-50');
                btn.classList.add('bg-[#094174]', 'text-white', 'hover:bg-[#105DA3]');
            }
        } catch (error) {
            console.error('Follow Toggle Failed:', error.message);

            // Rollback
            btn.dataset.following = isFollowing ? 'true' : 'false';
            if (isFollowing) {
                btn.textContent = 'Following';
                btn.classList.remove('bg-[#094174]', 'text-white', 'hover:bg-[#105DA3]');
                btn.classList.add('bg-white', 'text-[#094174]', 'hover:bg-gray-50');
            } else {
                btn.textContent = 'Follow';
                btn.classList.remove('bg-white', 'text-[#094174]', 'hover:bg-gray-50');
                btn.classList.add('bg-[#094174]', 'text-white', 'hover:bg-[#105DA3]');
            }
        }
    }
}
