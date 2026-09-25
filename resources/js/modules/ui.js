export function initUi() {
    document.addEventListener('alpine:init', () => {
        Alpine.data('appModal', () => ({
            open: false,
            init() {
                this.$watch('open', (value) => {
                    document.body.classList.toggle('overflow-hidden', value);
                });
            },
            openModal() {
                this.open = true;
            },
            closeModal() {
                this.open = false;
            },
        }));

        Alpine.data('confirmAction', (message) => ({
            confirm() {
                if (!message || window.confirm(message)) {
                    this.$el.submit();
                }
            },
        }));

        Alpine.data('tabs', () => ({
            active: '',
            setActive(name) {
                this.active = name;
            },
        }));

        Alpine.data('selectAll', () => ({
            all: false,
            toggle() {
                this.$root.querySelectorAll('input[data-bulk-item]').forEach((cb) => {
                    cb.checked = this.all;
                    cb.dispatchEvent(new Event('change', { bubbles: true }));
                });
            },
            update() {
                const boxes = [...this.$root.querySelectorAll('input[data-bulk-item]')];
                this.all = boxes.length > 0 && boxes.every((cb) => cb.checked);
            },
        }));

        Alpine.data('characterCounter', (max = 255) => ({
            length: 0,
            get remaining() {
                return Math.max(0, max - this.length);
            },
        }));

        Alpine.data('dropdown', () => ({
            open: false,
            close() {
                this.open = false;
            },
            toggle() {
                this.open = !this.open;
            },
        }));

        Alpine.data('copyButton', () => ({
            copied: false,
            copy() {
                const value = this.$el.dataset.copy;
                navigator.clipboard.writeText(value).then(() => {
                    this.copied = true;
                    setTimeout(() => (this.copied = false), 1500);
                });
            },
        }));

        Alpine.data('reveal', () => ({
            show: false,
        }));
    });
}
