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
    }

    switchTab(tabName) {
        // 1. Sembunyikan semua konten
        document.querySelectorAll('.tab-content').forEach((el) => {
            el.classList.remove('block');
            el.classList.add('hidden');
        });

        // 2. Tampilkan konten yang dipilih
        document.getElementById('content-' + tabName).classList.remove('hidden');
        document.getElementById('content-' + tabName).classList.add('block');

        // 3. Reset style semua tombol tab (jadikan abu-abu)
        document.querySelectorAll('.tab-btn').forEach((btn) => {
            btn.classList.remove('border-[#094174]', 'text-[#094174]', 'font-bold');
            btn.classList.add('border-transparent', 'text-gray-400', 'font-semibold');
        });

        // 4. Bikin tombol yang diklik jadi aktif (biru tebal)
        const activeBtn = document.getElementById('btn-' + tabName);
        activeBtn.classList.remove('border-transparent', 'text-gray-400', 'font-semibold');
        activeBtn.classList.add('border-[#094174]', 'text-[#094174]', 'font-bold');
    }
}
