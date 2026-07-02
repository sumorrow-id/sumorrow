export class CommentModal {
    /**
     * Initializes the CommentModal class.
     * @param {HTMLElement} modal
     * @param {HTMLElement} modalContent
     * @param {NodeListOf<HTMLElement>} closeTriggers
     */
    constructor(modal, modalContent, closeTriggers) {
        if (!modal || !modalContent) return;

        this.modal = modal;
        this.modalContent = modalContent;
        this.closeTriggers = closeTriggers;

        this.init();
    }

    init() {
        this.closeTriggers.forEach((trigger) => {
            trigger.addEventListener('click', () => this.close());
        });
    }

    open() {
        this.modal.classList.remove('hidden');
        this.modal.classList.add('flex');

        // Trigger animations
        setTimeout(() => {
            this.modalContent.classList.remove('scale-95', 'opacity-0');
            this.modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    close() {
        this.modalContent.classList.remove('scale-100', 'opacity-100');
        this.modalContent.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            this.modal.classList.add('hidden');
            this.modal.classList.remove('flex');
        }, 300); // Wait for transition
    }
}
