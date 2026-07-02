export class ConfirmSubmit {
    /**
     * Initializes the ConfirmSubmit class.
     * @param {HTMLElement} modal
     * @param {HTMLElement} titleEl
     * @param {HTMLElement} messageEl
     * @param {HTMLButtonElement} cancelBtn
     * @param {HTMLButtonElement} yesBtn
     * @param {HTMLElement} backdrop
     * @param {NodeListOf<HTMLFormElement>} forms
     */
    constructor(modal, titleEl, messageEl, cancelBtn, yesBtn, backdrop, forms) {
        if (!modal || !cancelBtn || !yesBtn || !forms.length) return;

        this.modal = modal;
        this.titleEl = titleEl;
        this.messageEl = messageEl;
        this.cancelBtn = cancelBtn;
        this.yesBtn = yesBtn;
        this.backdrop = backdrop;
        this.forms = forms;
        this.pendingForm = null;

        this.init();
    }

    init() {
        this.forms.forEach((form) => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.pendingForm = form;
                this.open(form);
            });
        });

        this.yesBtn.addEventListener('click', () => {
            const form = this.pendingForm;
            this.close();
            if (form) form.submit();
        });

        this.cancelBtn.addEventListener('click', () => this.close());

        if (this.backdrop) {
            this.backdrop.addEventListener('click', () => this.close());
        }
    }

    open(form) {
        const { confirmTitle, confirmMessage, confirmLabel, confirmVariant } = form.dataset;

        if (this.titleEl) this.titleEl.textContent = confirmTitle || 'Are you sure?';
        if (this.messageEl) this.messageEl.textContent = confirmMessage || '';
        this.yesBtn.textContent = confirmLabel || 'Confirm';

        const isDanger = confirmVariant === 'danger';
        this.yesBtn.classList.toggle('bg-red-500', isDanger);
        this.yesBtn.classList.toggle('hover:bg-red-600', isDanger);
        this.yesBtn.classList.toggle('bg-[#094174]', !isDanger);
        this.yesBtn.classList.toggle('hover:bg-[#105DA3]', !isDanger);

        this.modal.classList.remove('hidden');
        this.modal.classList.add('flex');
    }

    close() {
        this.modal.classList.add('hidden');
        this.modal.classList.remove('flex');
        this.pendingForm = null;
    }
}
