export class FilterDrawer {
    /**
     * Initializes the FilterDrawer class.
     * @param {HTMLButtonElement} filterBtn
     * @param {HTMLButtonElement} closeFilterBtn
     * @param {HTMLElement} filterDrawer
     * @param {HTMLElement} filterBackdrop
     * @param {HTMLFormElement} form
     */
    constructor(filterBtn, closeFilterBtn, filterDrawer, filterBackdrop, form) {
        if (!filterDrawer || !filterBackdrop || !form) return;

        this.filterBtn = filterBtn;
        this.closeFilterBtn = closeFilterBtn;
        this.filterDrawer = filterDrawer;
        this.filterBackdrop = filterBackdrop;
        this.form = form;

        this.init();
    }

    openDrawer() {
        this.filterBackdrop.classList.remove('hidden');
        setTimeout(() => {
            this.filterDrawer.classList.remove('translate-x-full');
        }, 10); // Small delay to allow display to toggle before transform
    }

    closeDrawer() {
        this.filterDrawer.classList.add('translate-x-full');
        setTimeout(() => {
            this.filterBackdrop.classList.add('hidden');
            if (window.innerWidth < 1024) {
                this.form.submit();
            }
        }, 300);
    }

    init() {
        if (this.filterBtn) this.filterBtn.addEventListener('click', () => this.openDrawer());
        if (this.closeFilterBtn) this.closeFilterBtn.addEventListener('click', () => this.closeDrawer());
        if (this.filterBackdrop) this.filterBackdrop.addEventListener('click', () => this.closeDrawer());
    }
}
