export class HomeWeatherHero {
    /**
     * Initializes the HomeWeatherHero class.
     * @param {HTMLElement} weatherWidgetElement
     */
    constructor(weatherWidgetElement) {
        if (!weatherWidgetElement) return;

        this.el = weatherWidgetElement;
        this.weatherData = JSON.parse(this.el.dataset.weather || '[]');
        this.heroImages = JSON.parse(this.el.dataset.heroImages || '[]');

        this.init();
    }

    init() {
        const weatherData = this.weatherData;

        let currentIndex = 0;
        const locationEl = document.getElementById('weather-location');
        const tempEl = document.getElementById('weather-temp');
        const widgetEl = document.getElementById('weather-widget');
        const contentEl = document.getElementById('weather-content');
        const heroImageEl = document.getElementById('hero-image');

        function fetchWeatherAndUpdate(index) {
            const data = weatherData[index];
            locationEl.innerHTML = data.loc;
            widgetEl.href = data.url;

            fetch(data.weatherUrl)
                .then(res => {
                    if (!res.ok) throw new Error('API Error');
                    return res.json();
                })
                .then(apiData => {
                    tempEl.innerHTML = apiData.temp;
                })
                .catch(() => {
                    tempEl.innerHTML = data.temp;
                });
        }

        if (widgetEl && contentEl && weatherData.length > 0) {
            // Inisialisasi awal
            fetchWeatherAndUpdate(currentIndex);

            setInterval(() => {
                contentEl.classList.remove('opacity-100');
                contentEl.classList.add('opacity-0');

                setTimeout(() => {
                    currentIndex = (currentIndex + 1) % weatherData.length;
                    fetchWeatherAndUpdate(currentIndex);

                    contentEl.classList.remove('opacity-0');
                    contentEl.classList.add('opacity-100');
                }, 500);
            }, 8000);
        }

        // Hero image carousel — crossfades through the 5 curated images only.
        const heroImages = this.heroImages;
        const heroNextEl = document.getElementById('hero-image-next');

        if (heroImageEl && heroNextEl && heroImages.length > 1) {
            // Preload every image up front so a swap never shows a loading flash.
            heroImages.forEach((src) => {
                const img = new Image();
                img.src = src;
            });

            let heroIndex = 0;
            let frontEl = heroImageEl; // currently visible layer
            let backEl = heroNextEl; // hidden layer we fade the next image into

            function crossfadeHero() {
                heroIndex = (heroIndex + 1) % heroImages.length;
                const nextSrc = heroImages[heroIndex];

                // Only start the crossfade once the next image is actually decoded.
                const loader = new Image();
                loader.onload = () => {
                    backEl.src = nextSrc;
                    backEl.classList.replace('opacity-0', 'opacity-100');
                    frontEl.classList.replace('opacity-100', 'opacity-0');

                    // Swap roles for the next cycle.
                    [frontEl, backEl] = [backEl, frontEl];
                };
                loader.src = nextSrc; // cached → onload fires immediately
            }

            // Offset from the 8s weather cycle so the two never change in lockstep.
            setInterval(crossfadeHero, 6000);
        }

        // Elevation slider logic
        const elevationSlider = document.getElementById('elevation-slider');
        const elevationValue = document.getElementById('elevation-value');

        if (elevationSlider && elevationValue) {
            elevationSlider.addEventListener('input', function(e) {
                if (e.target.value === '0') {
                    elevationValue.textContent = 'All Elevations';
                } else {
                    elevationValue.textContent = e.target.value + ' mdpl';
                }
            });
        }

        // Floating Mountain Carousel Logic
        const mtnCards = document.querySelectorAll('.mtn-card');
        const btnPrevMtn = document.getElementById('btn-prev-mtn');
        const btnNextMtn = document.getElementById('btn-next-mtn');

        if (mtnCards.length > 0) {
            let currentMtnIndex = 0;
            const totalMtns = mtnCards.length;

            function updateCarousel() {
                mtnCards.forEach((card, i) => {
                    // Reset classes, kita pakai transform manual di javascript agar lebih dinamis posisinya (sejajar/bertumpuk)
                    card.className =
                        "mtn-card overflow-hidden group shadow-2xl cursor-pointer block transition-all duration-700 ease-[cubic-bezier(0.25,1,0.5,1)] absolute right-0 top-1/2 w-[160px] sm:w-[200px] h-[220px] sm:h-[260px] rounded-[2rem]";

                    let diff = (i - currentMtnIndex) % totalMtns;
                    if (diff < 0) diff += totalMtns;

                    if (diff === 0) {
                        // Posisi 1 (Paling Kiri yang terlihat, Paling Depan & Terbesar)
                        card.classList.add('z-30', 'opacity-100', 'pointer-events-auto');
                        card.style.transform = 'translate(calc(-200% - 4rem), -35%) scale(1.15)';
                    } else if (diff === 1) {
                        // Posisi 2 (Tengah, Sedikit Menjauh / Lebih Kecil)
                        card.classList.add('z-20', 'opacity-100', 'pointer-events-auto');
                        card.style.transform = 'translate(calc(-100% - 1.5rem), -65%) scale(0.95)';
                    } else if (diff === 2) {
                        // Posisi 3 (Paling Kanan layar, Paling Distant / Terkecil)
                        card.classList.add('z-10', 'opacity-100', 'pointer-events-auto');
                        card.style.transform = 'translate(0, -45%) scale(0.8)';
                    } else if (diff === totalMtns - 1) {
                        // Posisi Sebelumnya (Menghilang keluar ke kiri)
                        card.classList.add('z-0', 'opacity-0', 'pointer-events-none');
                        card.style.transform = 'translate(calc(-300% - 6rem), -35%) scale(1.15)';
                    } else {
                        // Sisanya disembunyikan (Muncul dari luar kanan)
                        card.classList.add('z-0', 'opacity-0', 'pointer-events-none');
                        card.style.transform = 'translate(calc(100% + 1rem), -50%) scale(0.7)';
                    }
                });
            }

            // Inisialisasi awal
            updateCarousel();

            // Next = Move forward (card indeks 1 maju jadi indeks 0)
            btnNextMtn.addEventListener('click', () => {
                currentMtnIndex = (currentMtnIndex + 1) % totalMtns;
                updateCarousel();
            });

            // Prev = Move backward (card yg hilang muncul lagi)
            btnPrevMtn.addEventListener('click', () => {
                currentMtnIndex = (currentMtnIndex - 1 + totalMtns) % totalMtns;
                updateCarousel();
            });
        }
    }
}
