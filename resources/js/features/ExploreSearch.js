import { debounce } from '../utils/debounce';

export class ExploreSearch {
    /**
     * Initializes the ExploreSearch class.
     * @param {HTMLFormElement} formElement
     * @param {HTMLInputElement} inputElement
     */
    constructor(formElement, inputElement) {
        if (!formElement || !inputElement) return;

        this.form = formElement;
        this.input = inputElement;

        this.init();
    }

    init() {
        this.input.addEventListener('input', debounce(() => {
            this.form.submit();
        }, 500));

        // Refocus the input element on document load so typing isn't interrupted too badly
        window.addEventListener('load', () => {
            if (this.input.value) {
                this.input.focus();
                // Move cursor to the end of the input value
                const val = this.input.value;
                this.input.value = '';
                this.input.value = val;
            }
        });
    }
}

