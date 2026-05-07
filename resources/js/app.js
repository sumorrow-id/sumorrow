import './bootstrap';
import { ExploreSearch } from './features/ExploreSearch';

document.addEventListener('DOMContentLoaded', () => {
    const exploreForm = document.getElementById('explore-form');
    const searchInput = document.getElementById('search-input');

    if (exploreForm && searchInput) {
        new ExploreSearch(exploreForm, searchInput);
    }
});
