export const theme = {
    key: 'pp-theme',

    current() {
        return localStorage.getItem(this.key) || this.system();
    },

    system() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    },

    apply(value) {
        document.documentElement.classList.toggle('dark', value === 'dark');
    },

    init() {
        this.apply(this.current());
        document.addEventListener('alpine:init', () => {
            Alpine.data('themeToggle', () => ({
                theme: this.current(),
                toggle() {
                    this.theme = this.theme === 'dark' ? 'light' : 'dark';
                    localStorage.setItem(this.key, this.theme);
                    theme.apply(this.theme);
                },
            }));
        });
    },
};
