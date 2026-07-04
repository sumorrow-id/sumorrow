const BURGER_PATH =
    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
const CLOSE_PATH =
    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';

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
        this.isOpen = false;

        this.init();
    }

    init() {
        this.btn.setAttribute('aria-expanded', 'false');

        this.btn.addEventListener('click', () => {
            this.isOpen ? this.close() : this.open();
        });

        // Tapping a menu link (or submitting logout) should collapse the menu —
        // relevant for same-page anchors where no navigation reload happens.
        this.menu.addEventListener('click', (e) => {
            if (e.target.closest('a')) this.close();
        });

        document.addEventListener('click', (e) => {
            if (this.isOpen && !this.menu.contains(e.target) && !this.btn.contains(e.target)) {
                this.close();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.close();
        });

        // Leaving the mobile breakpoint while open would otherwise strand the
        // menu expanded (and the icon as an X) when resizing back down.
        window.matchMedia('(min-width: 768px)').addEventListener('change', (mq) => {
            if (mq.matches) this.close();
        });
    }

    open() {
        this.isOpen = true;
        this.btn.setAttribute('aria-expanded', 'true');
        this.icon.innerHTML = CLOSE_PATH;
        this.menu.classList.remove('opacity-0');
        this.menu.classList.add('opacity-100');
        // Measure the real content height instead of a fixed cap — a fixed
        // max-height clipped the bottom items on longer menus.
        this.menu.style.maxHeight = this.menu.scrollHeight + 'px';
    }

    close() {
        if (!this.isOpen) return;

        this.isOpen = false;
        this.btn.setAttribute('aria-expanded', 'false');
        this.icon.innerHTML = BURGER_PATH;
        this.menu.classList.remove('opacity-100');
        this.menu.classList.add('opacity-0');
        this.menu.style.maxHeight = '0px';
    }
}
