import './bootstrap';

import Alpine from 'alpinejs';
import { theme } from './modules/theme';
import { toast } from './modules/toast';
import { initMarkdownEditor } from './modules/markdown';
import { initCharts } from './modules/charts';
import { initUi } from './modules/ui';

window.Alpine = Alpine;
window.toast = toast;

Alpine.store('toast', toast);

// Register all Alpine data components.
theme.init();
initMarkdownEditor();
initCharts();
initUi();

// Expose any server-flashed toast messages.
document.addEventListener('DOMContentLoaded', () => {
    const node = document.getElementById('flash-toast');

    if (node) {
        try {
            const payload = JSON.parse(node.textContent);
            toast.push(payload);
        } catch (error) {
            // Ignore malformed flash payloads.
        }
    }
});

Alpine.start();
