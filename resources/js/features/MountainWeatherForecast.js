export class MountainWeatherForecast {
    /**
     * Initializes the MountainWeatherForecast class.
     * @param {HTMLElement} weatherContainerElement
     */
    constructor(weatherContainerElement) {
        if (!weatherContainerElement) return;

        this.el = weatherContainerElement;

        this.init();
    }

    init() {
        const weatherContainer = this.el;
        const weatherUrl = this.el.dataset.weatherUrl;
        const forecastUrl = this.el.dataset.forecastUrl;

        // Scroll: pin lokasi -> map section
        const heroLocationPin = document.getElementById('hero-location-pin');
        const scrollToMap = () => document.getElementById('map-location')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        if (heroLocationPin) {
            heroLocationPin.addEventListener('click', scrollToMap);
            heroLocationPin.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); scrollToMap(); } });
        }

        // Scroll: kartu cuaca hero -> forecast section
        const heroWeatherCard = document.getElementById('hero-weather-card');
        const scrollToForecast = () => document.getElementById('weather-forecast')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        if (heroWeatherCard) {
            heroWeatherCard.addEventListener('click', scrollToForecast);
            heroWeatherCard.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); scrollToForecast(); } });
        }

        // Back to Top button
        const backToTopBtn = document.getElementById('back-to-top');
        if (backToTopBtn) {
            const hiddenClasses = ['opacity-0', 'translate-y-4', 'scale-90', 'pointer-events-none'];
            const toggleBackToTop = () => {
                if (window.scrollY > 400) { backToTopBtn.classList.remove(...hiddenClasses); }
                else { backToTopBtn.classList.add(...hiddenClasses); }
            };
            toggleBackToTop();
            window.addEventListener('scroll', toggleBackToTop, { passive: true });
            backToTopBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
        }

        // SVG weather icon helper
        const getWeatherSvg = (iconCode, classes) => {
            const code = iconCode.substring(0, 2);
            const isDay = iconCode.endsWith('d');
            const sunSvg = `<svg class="${classes}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="5" fill="#FBBF24"/><path class="weather-spin" d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41" stroke="#FBBF24" stroke-width="2" stroke-linecap="round"/></svg>`;
            const moonSvg = `<svg class="${classes}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path class="weather-pulse" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" fill="#93C5FD"/></svg>`;
            const cloudPath = `<path class="weather-bob" d="M17.5 19H9a7 7 0 116.71-9h1.79a4.5 4.5 0 110 9z" fill="#E5E7EB"/>`;
            const partlyCloudySvg = `<svg class="${classes}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">${isDay ? '<circle class="weather-pulse" cx="8" cy="8" r="4" fill="#FBBF24"/>' : '<path class="weather-pulse" d="M11 4.5A5.5 5.5 0 114.5 11 4.5 4.5 0 0011 4.5z" fill="#93C5FD"/>'}${cloudPath}</svg>`;
            const cloudSvg = `<svg class="${classes}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">${cloudPath}</svg>`;
            const rainSvg = `<svg class="${classes}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">${cloudPath}<path class="weather-rain" d="M9 19v3m4-3v3m4-3v3" stroke="#60A5FA" stroke-width="2" stroke-linecap="round"/></svg>`;
            const thunderSvg = `<svg class="${classes}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">${cloudPath}<path class="weather-flash" d="M13 14l-3 5h4l-3 5" stroke="#FBBF24" stroke-width="1.5" fill="#FCD34D"/></svg>`;
            const snowSvg = `<svg class="${classes}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">${cloudPath}<circle class="weather-rain" cx="9" cy="21" r="1.5" fill="#93C5FD"/><circle class="weather-rain" cx="13" cy="21" r="1.5" fill="#93C5FD"/><circle class="weather-rain" cx="17" cy="21" r="1.5" fill="#93C5FD"/></svg>`;
            switch (code) {
                case '01': return isDay ? sunSvg : moonSvg;
                case '02': return partlyCloudySvg;
                case '03': case '04': case '50': return cloudSvg;
                case '09': case '10': return rainSvg;
                case '11': return thunderSvg;
                case '13': return snowSvg;
                default: return cloudSvg;
            }
        };

        const showHeroCard = (icon, temp, description) => {
            const heroCard = document.getElementById('hero-weather-card');
            const heroIconContainer = document.getElementById('hero-weather-icon-container');
            const heroTemp = document.getElementById('hero-weather-temp');
            const heroDesc = document.getElementById('hero-weather-desc');
            if (!heroCard) { return; }
            heroIconContainer.innerHTML = getWeatherSvg(icon, 'w-12 h-12 md:w-14 md:h-14 drop-shadow-md');
            heroTemp.innerHTML = temp + 'C';
            heroDesc.innerHTML = description;
            heroCard.classList.remove('hidden');
            heroCard.classList.add('flex');
        };

        const renderForecast = (list) => {
            // The forecast section shows the NEXT 3 days — today's weather
            // already lives in the hero card, so entries dated today (or
            // earlier) are excluded before grouping.
            const startOfTomorrow = new Date();
            startOfTomorrow.setHours(24, 0, 0, 0);

            const dailyData = {};
            list.forEach(item => {
                const itemDate = new Date(item.dt * 1000);
                if (itemDate < startOfTomorrow) { return; }

                const date = itemDate.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
                if (!dailyData[date]) { dailyData[date] = { temp: [], icon: item.weather[0].icon, desc: item.weather[0].main, iconHourGap: 24 }; }
                dailyData[date].temp.push(item.main.temp);

                // Represent the day with the entry closest to midday, not the
                // 00:00 slot (which always carries a night icon).
                const hourGap = Math.abs(itemDate.getHours() - 12);
                if (hourGap < dailyData[date].iconHourGap) {
                    dailyData[date].iconHourGap = hourGap;
                    dailyData[date].icon = item.weather[0].icon;
                    dailyData[date].desc = item.weather[0].main;
                }
            });
            const days = Object.keys(dailyData).slice(0, 3);
            weatherContainer.innerHTML = days.map(day => {
                const info = dailyData[day];
                const maxTemp = Math.round(Math.max(...info.temp));
                const minTemp = Math.round(Math.min(...info.temp));
                return `<div class="bg-white p-4 rounded-xl border border-gray-100 flex flex-col items-center justify-center text-center shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-[#2A9D8F]">
                    <span class="font-semibold text-[#001E3A] mb-1">${day}</span>
                    ${getWeatherSvg(info.icon, 'w-16 h-16 drop-shadow-sm')}
                    <span class="text-sm font-medium text-gray-700 capitalize mt-1">${info.desc}</span>
                    <div class="mt-2 text-[#2A9D8F] font-bold">${maxTemp}&deg; <span class="text-gray-400 text-sm font-normal">/ ${minTemp}&deg;</span></div>
                </div>`;
            }).join('');
        };

        // Current weather → hero card (same proxy as home.blade.php)
        fetch(weatherUrl)
            .then(res => { if (!res.ok) { throw new Error('Weather error'); } return res.json(); })
            .then(data => showHeroCard(data.icon ?? '01d', data.temp, data.description ?? ''))
            .catch(() => showHeroCard('01d', '22&deg;', 'Clear'));

        // 3-day forecast (the 3 days AFTER today) → forecast section
        fetch(forecastUrl)
            .then(res => { if (!res.ok) { throw new Error('Forecast error'); } return res.json(); })
            .then(data => renderForecast(data.list ?? []))
            .catch(() => renderForecast([
                { dt: Date.now() / 1000 + 86400, main: { temp: 22 }, weather: [{ icon: '01d', main: 'Clear' }] },
                { dt: Date.now() / 1000 + 172800, main: { temp: 23 }, weather: [{ icon: '03d', main: 'Clouds' }] },
                { dt: Date.now() / 1000 + 259200, main: { temp: 20 }, weather: [{ icon: '10d', main: 'Rain' }] },
            ]));
    }
}
