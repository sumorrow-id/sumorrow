export class FlashBanner {
    /**
     * Initializes the FlashBanner class.
     * @param {HTMLElement} element
     */
    constructor(element) {
        if (!element) return;

        this.element = element;

        this.init();
    }

    init() {
        setTimeout(() => {
            this.element.classList.add('opacity-0', '-translate-y-2');
            setTimeout(() => this.element.remove(), 300);
        }, 6000);
    }
}
