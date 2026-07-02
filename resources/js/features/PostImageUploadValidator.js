export class PostImageUploadValidator {
    /**
     * Initializes the PostImageUploadValidator class.
     * @param {HTMLInputElement} imagesInput
     * @param {HTMLElement} errorElement
     * @param {HTMLButtonElement} submitBtn
     * @param {HTMLFormElement} formElement
     */
    constructor(imagesInput, errorElement, submitBtn, formElement) {
        if (!imagesInput || !errorElement || !submitBtn || !formElement) return;

        this.imagesInput = imagesInput;
        this.errorElement = errorElement;
        this.submitBtn = submitBtn;
        this.form = formElement;

        this.init();
    }

    init() {
        this.imagesInput.addEventListener('change', (e) => this.validateImages(e));

        this.form.addEventListener('submit', (e) => {
            if (this.submitBtn.disabled) {
                e.preventDefault();
            }
        });
    }

    validateImages(e) {
        const files = e.target.files;
        let errorMsg = '';
        let totalSize = 0;

        if (files.length > 12) {
            errorMsg = 'You can only upload a maximum of 12 images.';
        } else {
            for (let i = 0; i < files.length; i++) {
                totalSize += files[i].size;
                if (files[i].size > 2 * 1024 * 1024) { // 2MB
                    errorMsg = 'Each image must be smaller than 2MB.';
                    break;
                }
            }
            if (!errorMsg && totalSize > 24 * 1024 * 1024) {
                errorMsg = 'Total size of all images cannot exceed 24MB.';
            }
        }

        if (errorMsg) {
            this.errorElement.textContent = errorMsg;
            this.errorElement.classList.remove('hidden');
            this.submitBtn.disabled = true;
            this.submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            this.errorElement.classList.add('hidden');
            this.submitBtn.disabled = false;
            this.submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
}
