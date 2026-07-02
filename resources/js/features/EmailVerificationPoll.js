export class EmailVerificationPoll {
    /**
     * Initializes the EmailVerificationPoll class.
     * @param {HTMLElement} element
     */
    constructor(element) {
        if (!element) return;

        this.el = element;
        this.redirectUrl = this.el.dataset.redirectUrl;

        this.init();
    }

    init() {
        setInterval(() => {
            fetch('/api/check-verification')
                .then((response) => response.json())
                .then((data) => {
                    if (data.verified) {
                        window.location.href = this.redirectUrl;
                    }
                });
        }, 3000);
    }
}
