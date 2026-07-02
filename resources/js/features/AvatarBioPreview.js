export class AvatarBioPreview {
    /**
     * Initializes the AvatarBioPreview class.
     * @param {?HTMLInputElement} avatarInput
     * @param {?HTMLImageElement} avatarPreview
     * @param {?HTMLInputElement} coverInput
     * @param {?HTMLImageElement} coverPreview
     * @param {?HTMLTextAreaElement} bioInput
     * @param {?HTMLElement} bioCounter
     */
    constructor(avatarInput, avatarPreview, coverInput, coverPreview, bioInput, bioCounter) {
        this.avatarInput = avatarInput;
        this.avatarPreview = avatarPreview;
        this.coverInput = coverInput;
        this.coverPreview = coverPreview;
        this.bioInput = bioInput;
        this.bioCounter = bioCounter;

        this.init();
    }

    init() {
        this.wireImagePreview(this.avatarInput, this.avatarPreview);
        this.wireImagePreview(this.coverInput, this.coverPreview);
        this.wireBioCounter();
    }

    wireImagePreview(input, preview) {
        if (!input || !preview) return;

        input.addEventListener('change', () => {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        });
    }

    wireBioCounter() {
        if (!this.bioInput || !this.bioCounter) return;

        const updateCounter = () => {
            this.bioCounter.textContent = `${this.bioInput.value.length} / 500 characters`;
        };

        this.bioInput.addEventListener('input', updateCounter);
        updateCounter();
    }
}
