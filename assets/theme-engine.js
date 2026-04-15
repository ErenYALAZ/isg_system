(function() {
    const storageKey = 'maisg-theme-preference';

    const getTheme = () => {
        if (localStorage.getItem(storageKey)) {
            return localStorage.getItem(storageKey);
        }
        return window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
    };

    const setTheme = (theme) => {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem(storageKey, theme);
        
        // Update Chart colors if they exist
        if (typeof updateChartsTheme === 'function') {
            updateChartsTheme(theme);
        }
    };

    // Initialize theme immediately
    setTheme(getTheme());

    // Wait for DOM to add button listeners
    window.addEventListener('DOMContentLoaded', () => {
        const toggles = document.querySelectorAll('.js-theme-toggle');
        toggles.forEach(btn => {
            btn.addEventListener('click', () => {
                const current = document.documentElement.getAttribute('data-theme');
                const next = current === 'dark' ? 'light' : 'dark';
                setTheme(next);
            });
        });
    });
})();
