export class CommunityForum {
    /**
     * Initializes the CommunityForum class.
     * @param {NodeListOf<HTMLElement>} tabTriggers
     * @param {?HTMLElement} modal
     * @param {NodeListOf<HTMLElement>} modalOpenTriggers
     * @param {NodeListOf<HTMLElement>} modalCloseTriggers
     * @param {?HTMLElement} sidebarOverlay
     * @param {?HTMLElement} sidebarDrawer
     * @param {NodeListOf<HTMLElement>} sidebarToggleTriggers
     */
    constructor(tabTriggers, modal, modalOpenTriggers, modalCloseTriggers, sidebarOverlay, sidebarDrawer, sidebarToggleTriggers) {
        if (!tabTriggers || !tabTriggers.length) return;

        this.tabTriggers = tabTriggers;
        this.modal = modal;
        this.modalOpenTriggers = modalOpenTriggers;
        this.modalCloseTriggers = modalCloseTriggers;
        this.sidebarOverlay = sidebarOverlay;
        this.sidebarDrawer = sidebarDrawer;
        this.sidebarToggleTriggers = sidebarToggleTriggers;
        this.isMobileSidebarOpen = false;

        this.init();
    }

    init() {
        this.tabTriggers.forEach((trigger) => {
            trigger.addEventListener('click', () => this.switchTab(trigger.dataset.forumTab));
        });

        if (this.modal) {
            this.modalOpenTriggers.forEach((trigger) => trigger.addEventListener('click', () => this.openModal()));
            this.modalCloseTriggers.forEach((trigger) => trigger.addEventListener('click', () => this.closeModal()));
        }

        if (this.sidebarOverlay && this.sidebarDrawer) {
            this.sidebarToggleTriggers.forEach((trigger) => trigger.addEventListener('click', () => this.toggleMobileSidebar()));
        }

        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab');

        this.switchTab(activeTab === 'community' ? 'community' : 'explore');
    }

    switchTab(tab) {
        const tabs = ['explore', 'community'];

        tabs.forEach((t) => {
            const btn = document.getElementById('tab-' + t);
            const content = document.getElementById('content-' + t);
            if (!btn || !content) return;

            if (t === tab) {
                btn.className = `pb-3 border-b-2 transition-all duration-300 text-sm sm:text-base cursor-pointer border-[#094174] text-[#094174] font-bold whitespace-nowrap`;
                content.classList.remove('hidden');
                content.classList.add('grid');
            } else {
                btn.className = `pb-3 border-b-2 transition-all duration-300 text-sm sm:text-base cursor-pointer border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap`;
                content.classList.add('hidden');
                content.classList.remove('grid');
            }
        });
    }

    openModal() {
        this.modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    closeModal() {
        this.modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    toggleMobileSidebar() {
        this.isMobileSidebarOpen = !this.isMobileSidebarOpen;

        if (this.isMobileSidebarOpen) {
            this.sidebarOverlay.classList.remove('hidden');
            // small delay to allow display block to process before adding opacity
            setTimeout(() => this.sidebarOverlay.classList.remove('opacity-0'), 10);

            this.sidebarDrawer.classList.remove('translate-x-full');
            document.body.style.overflow = 'hidden'; // prevent background scrolling
        } else {
            this.sidebarOverlay.classList.add('opacity-0');
            this.sidebarDrawer.classList.add('translate-x-full');
            document.body.style.overflow = '';
            setTimeout(() => this.sidebarOverlay.classList.add('hidden'), 300);
        }
    }
}
