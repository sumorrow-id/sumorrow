export class HamburgerMenu {
    /**
     * Initializes the HamburgerMenu class.
     * @param {HTMLButtonElement} btn
     * @param {HTMLElement} menu
     * @param {SVGElement} icon
     */
    constructor(btn, menu, icon) {
        if (!btn || !menu || !icon) return;

        this.btn = btn;
        this.menu = menu;
        this.icon = icon;

        this.init();
    }

    init() {
        this.btn.addEventListener('click', () => {
            const isClosed = this.menu.classList.contains('max-h-0');

            if (isClosed) {
                // Open menu
                this.icon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
                this.menu.classList.remove('max-h-0', 'opacity-0');
                this.menu.classList.add('max-h-[400px]', 'opacity-100');
            } else {
                // Close menu
                this.icon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
                this.menu.classList.remove('max-h-[400px]', 'opacity-100');
                this.menu.classList.add('max-h-0', 'opacity-0');
            }
        });
    }
}
