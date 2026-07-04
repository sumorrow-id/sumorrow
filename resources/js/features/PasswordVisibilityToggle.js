export class PasswordVisibilityToggle {
    /**
     * Initializes the PasswordVisibilityToggle class.
     * @param {HTMLButtonElement} toggleBtn
     * @param {HTMLInputElement} passwordInput
     * @param {HTMLElement} eyeIcon
     */
    constructor(toggleBtn, passwordInput, eyeIcon) {
        if (!toggleBtn || !passwordInput || !eyeIcon) return;

        this.toggleBtn = toggleBtn;
        this.passwordInput = passwordInput;
        this.eyeIcon = eyeIcon;

        this.init();
    }

    init() {
        this.toggleBtn.addEventListener('click', () => {
            const isPassword = this.passwordInput.getAttribute('type') === 'password';
            this.passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

            this.eyeIcon.classList.toggle('fa-eye-slash', !isPassword);
            this.eyeIcon.classList.toggle('fa-eye', isPassword);
        });
    }
}
