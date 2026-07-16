import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

/**
 * Map pin-picker for the admin mountain form. Clicking the map (or dragging
 * the pin) writes the position into the coordinates input using the DMS
 * format the rest of the app expects (see WeatherController::parseCoordinates
 * and StoreMountainRequest::COORDINATES_PATTERN):
 *   8 deg 16' 0" S, 115 deg 25' 0" E
 */
export class CoordinatesPicker {
    static INDONESIA_CENTER = [-2.5, 118];

    constructor(mapEl, input) {
        this.input = input;
        this.marker = null;

        this.map = L.map(mapEl).setView(CoordinatesPicker.INDONESIA_CENTER, 5);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 17,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        }).addTo(this.map);

        const existing = this.parseDMS(input.value);
        if (existing) {
            this.placeMarker(existing);
            this.map.setView(existing, 11);
        }

        this.map.on('click', (e) => {
            this.placeMarker(e.latlng);
            this.fillInput(e.latlng);
        });

        input.addEventListener('change', () => {
            const pos = this.parseDMS(input.value);
            if (pos) {
                this.placeMarker(pos);
                this.map.setView(pos, Math.max(this.map.getZoom(), 9));
            }
        });
    }

    placeMarker(latlng) {
        if (this.marker) {
            this.marker.setLatLng(latlng);
            return;
        }

        // divIcon with an inline SVG pin: Leaflet's default marker images
        // don't resolve under Vite without extra asset plumbing.
        const pin = L.divIcon({
            className: '',
            iconSize: [30, 42],
            iconAnchor: [15, 42],
            html: '<svg width="30" height="42" viewBox="0 0 30 42" xmlns="http://www.w3.org/2000/svg">'
                + '<path d="M15 0C6.7 0 0 6.7 0 15c0 11 15 27 15 27s15-16 15-27C30 6.7 23.3 0 15 0z" fill="#094174"/>'
                + '<circle cx="15" cy="15" r="6" fill="#fff"/></svg>',
        });

        this.marker = L.marker(latlng, { icon: pin, draggable: true }).addTo(this.map);
        this.marker.on('dragend', () => this.fillInput(this.marker.getLatLng()));
    }

    fillInput(latlng) {
        this.input.value = `${this.toDMS(latlng.lat, 'NS')}, ${this.toDMS(latlng.lng, 'EW')}`;
        this.input.dispatchEvent(new Event('input', { bubbles: true }));
    }

    toDMS(value, hemispheres) {
        const hemisphere = hemispheres[value < 0 ? 1 : 0];
        const abs = Math.abs(value);
        let degrees = Math.floor(abs);
        let minutesFloat = (abs - degrees) * 60;
        let minutes = Math.floor(minutesFloat);
        let seconds = Math.round((minutesFloat - minutes) * 60);
        if (seconds === 60) {
            seconds = 0;
            minutes += 1;
        }
        if (minutes === 60) {
            minutes = 0;
            degrees += 1;
        }

        return `${degrees} deg ${minutes}' ${seconds}" ${hemisphere}`;
    }

    parseDMS(value) {
        const matches = [...value.matchAll(/(\d+)\s*deg\s*(\d+)\s*'\s*(\d+(?:\.\d+)?)\s*"\s*([NSEW])/gi)];
        if (matches.length < 2) {
            return null;
        }

        const toDecimal = (m) => {
            const decimal = Number(m[1]) + Number(m[2]) / 60 + Number(m[3]) / 3600;
            return /[SW]/i.test(m[4]) ? -decimal : decimal;
        };

        return [toDecimal(matches[0]), toDecimal(matches[1])];
    }
}
