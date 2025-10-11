/**
 * Theme Management Module for Alpine.js
 * Manages light/dark theme switching, system preference detection, and persistence.
 */
const themeManager = {
    // State
    theme: 'system', // 'light', 'dark', or 'system'

    /**
     * Initializes the theme manager.
     */
    init() {
        this.theme = this.getStoredTheme() || 'system';
        this.applyTheme();

        // Watch for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (this.theme === 'system') {
                this.applyTheme();
            }
        });
    },

    /**
     * Retrieves the stored theme preference from localStorage.
     * @returns {string|null} 'light', 'dark', or null.
     */
    getStoredTheme() {
        return localStorage.getItem('theme');
    },

    /**
     * Detects if the system prefers a dark color scheme.
     * @returns {boolean} True if the system prefers dark mode.
     */
    isSystemDarkMode() {
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    },

    /**
     * Determines if the current theme is dark.
     * @returns {boolean} True if the current theme is dark.
     */
    isDark() {
        if (this.theme === 'system') {
            return this.isSystemDarkMode();
        }
        return this.theme === 'dark';
    },

    /**
     * Toggles the current theme between light and dark.
     */
    toggleTheme() {
        const newTheme = this.isDark() ? 'light' : 'dark';
        this.setTheme(newTheme);
    },

    /**
     * Sets the theme and applies it.
     * @param {string} theme - The theme to set ('light', 'dark', or 'system').
     */
    setTheme(theme) {
        this.theme = theme;
        this.saveTheme(theme);
        this.applyTheme();
    },

    /**
     * Saves the current theme preference to localStorage.
     * @param {string} theme - The theme to save.
     */
    saveTheme(theme) {
        localStorage.setItem('theme', theme);
    },

    /**
     * Applies the theme to the document's root element.
     */
    applyTheme() {
        const isDark = this.isDark();

        // Add a class to the body for smooth transitions
        document.body.classList.add('theme-transition');

        if (isDark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // Remove the transition class after the animation completes
        setTimeout(() => {
            document.body.classList.remove('theme-transition');
        }, 500);

        // Broadcast a custom event for other components to listen to
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: this.theme, isDark } }));
    }
};

export default themeManager;
