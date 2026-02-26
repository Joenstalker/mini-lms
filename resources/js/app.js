import './bootstrap';
import Alpine from 'alpinejs';
import * as Turbo from '@hotwired/turbo';

window.Alpine = Alpine;

// Start Turbo Drive - intercepts all link clicks and swaps the <body>
// without a full page reload.
Turbo.start();

// When Turbo loads a new page, we need to ensure Alpine is still running.
// We also restore the active link in the sidebar (which is data-turbo-permanent).
document.addEventListener('turbo:load', () => {
    // Update active nav link based on current URL
    const currentPath = window.location.pathname;
    document.querySelectorAll('#spa-sidebar a[href]').forEach(link => {
        const linkPath = new URL(link.href, window.location.origin).pathname;
        const isActive = currentPath === linkPath || currentPath.startsWith(linkPath + '/');
        // Re-apply active class
        if (isActive) {
            link.classList.add('bg-primary', 'text-primary-content', 'shadow-lg', 'shadow-primary/20');
            link.classList.remove('hover:bg-primary/10', 'hover:text-primary');
        } else {
            link.classList.remove('bg-primary', 'text-primary-content', 'shadow-lg', 'shadow-primary/20');
            link.classList.add('hover:bg-primary/10', 'hover:text-primary');
        }
    });
});

// Start Alpine - only once. Turbo handles subsequent page transitions.
Alpine.start();
