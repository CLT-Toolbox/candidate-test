import './bootstrap';

import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';
import Sortable from 'sortablejs';

window.Alpine = Alpine;
window.Sortable = Sortable;
window.axios = axios;

// Configure Axios CSRF
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

window.lucide = { createIcons, icons };
window.lucide.createIcons({ icons });

Alpine.start();
