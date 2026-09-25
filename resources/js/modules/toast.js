export const toast = {
    items: [],
    counter: 0,

    push({ type = 'success', title = '', message = '' }) {
        const id = ++this.counter;

        this.items.push({ id, type, title, message });
        window.setTimeout(() => this.dismiss(id), 5000);
    },

    dismiss(id) {
        this.items = this.items.filter((item) => item.id !== id);
    },

    success(title, message) {
        this.push({ type: 'success', title, message });
    },

    error(title, message) {
        this.push({ type: 'error', title, message });
    },

    info(title, message) {
        this.push({ type: 'info', title, message });
    },
};
