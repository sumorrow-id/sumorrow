/**
 * Explore page: fetch the visitor's location so the server can surface the
 * "Nearby Mountains" section. Coordinates are appended to the URL and the page
 * reloads once; the controller partitions the catalog into nearby vs. others.
 *
 * We only ask once per session — remembering the coords on success and a
 * dismissal on denial — so the browser permission prompt never nags.
 */
export class NearbyMountains {
    static COORDS_KEY = 'nearbyCoords';
    static DISMISSED_KEY = 'nearbyGeoDismissed';

    /**
     * @param {HTMLElement|null} triggerEl  Optional "show mountains near me" button.
     */
    constructor(triggerEl = null) {
        this.trigger = triggerEl;
        this.init();
    }

    init() {
        const url = new URL(window.location.href);

        // Server already has coordinates for this request — nothing to do.
        if (url.searchParams.has('lat') && url.searchParams.has('lng')) {
            return;
        }

        // Reuse coords from earlier this session without re-prompting.
        const stored = sessionStorage.getItem(NearbyMountains.COORDS_KEY);
        if (stored) {
            const { lat, lng } = JSON.parse(stored);
            this.redirectWithCoords(lat, lng);
            return;
        }

        // Manual button always re-asks, even after a prior dismissal.
        if (this.trigger) {
            this.trigger.addEventListener('click', () => this.locate());
        }

        if (!sessionStorage.getItem(NearbyMountains.DISMISSED_KEY)) {
            this.locate();
        }
    }

    locate() {
        if (!('geolocation' in navigator)) {
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = Number(position.coords.latitude.toFixed(5));
                const lng = Number(position.coords.longitude.toFixed(5));
                sessionStorage.setItem(NearbyMountains.COORDS_KEY, JSON.stringify({ lat, lng }));
                this.redirectWithCoords(lat, lng);
            },
            () => sessionStorage.setItem(NearbyMountains.DISMISSED_KEY, '1'),
            { timeout: 8000, maximumAge: 600000 },
        );
    }

    redirectWithCoords(lat, lng) {
        const url = new URL(window.location.href);
        url.searchParams.set('lat', lat);
        url.searchParams.set('lng', lng);
        window.location.assign(url.toString());
    }
}
