export class EmailValidityMessage {
    /**
     * Initializes the EmailValidityMessage class.
     * @param {HTMLInputElement} inputElement
     */
    constructor(inputElement) {
        if (!inputElement) return;

        this.input = inputElement;

        this.init();
    }

    init() {
        this.input.addEventListener('invalid', () => {
            if (this.input.value === '') {
                this.input.setCustomValidity('Email tidak boleh kosong');
            } else {
                this.input.setCustomValidity('Format email salah');
            }
        });

        this.input.addEventListener('input', () => {
            this.input.setCustomValidity('');
        });
    }
}
