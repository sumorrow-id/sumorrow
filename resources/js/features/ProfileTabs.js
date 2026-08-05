export class ProfileTabs {
    /**
     * Initializes the ProfileTabs class.
     * @param {NodeListOf<HTMLButtonElement>} tabButtons
     */
    constructor(tabButtons) {
        if (!tabButtons || !tabButtons.length) return;

        this.tabButtons = tabButtons;

        this.init();
    }

    init() {
        this.tabButtons.forEach((btn) => {
            btn.addEventListener('click', () => this.switchTab(btn.dataset.tab));
        });

        // Restore the tab named in the URL fragment (e.g. /profile#gear) so a
        // refresh — or a redirect back from adding/deleting gear — keeps the
        // user on the tab they were working in.
        this.switchTab(window.location.hash.replace('#', ''));
    }

    switchTab(tabName) {
        const content = tabName ? document.getElementById('content-' + tabName) : null;

        // Unknown fragment (or none): leave the default tab alone.
        if (!content) return;

        // 1. Sembunyikan semua konten
        document.querySelectorAll('.tab-content').forEach((el) => {
            el.classList.remove('block');
            el.classList.add('hidden');
        });

        // 2. Tampilkan konten yang dipilih
        content.classList.remove('hidden');
        content.classList.add('block');

        // 3. Reset style semua tombol tab (jadikan abu-abu)
        document.querySelectorAll('.tab-btn').forEach((btn) => {
            btn.classList.remove('border-[#094174]', 'text-[#094174]', 'font-bold');
            btn.classList.add('border-transparent', 'text-gray-400', 'font-semibold');
        });

        // 4. Bikin tombol yang diklik jadi aktif (biru tebal)
        const activeBtn = document.getElementById('btn-' + tabName);
        if (activeBtn) {
            activeBtn.classList.remove('border-transparent', 'text-gray-400', 'font-semibold');
            activeBtn.classList.add('border-[#094174]', 'text-[#094174]', 'font-bold');
        }

        // 5. Keep the fragment in sync so a refresh lands on the same tab.
        if (window.location.hash !== '#' + tabName) {
            window.history.replaceState(null, '', '#' + tabName);
        }
    }
}
