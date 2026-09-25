import DOMPurify from 'dompurify';
import { marked } from 'marked';

marked.setOptions({ breaks: true, gfm: true });

export function renderMarkdown(value) {
    return DOMPurify.sanitize(marked.parse(value || ''));
}

export function initMarkdownEditor() {
    document.addEventListener('alpine:init', () => {
        Alpine.data('markdownEditor', (initialValue = '', textareaRef = null) => ({
            tab: 'write',
            value: initialValue,

            get previewHtml() {
                return renderMarkdown(this.value);
            },

            insert(text) {
                const el = this.$refs[textareaRef] || this.$refs.editor;

                if (!el) {
                    return;
                }

                const start = el.selectionStart ?? this.value.length;
                const end = el.selectionEnd ?? this.value.length;
                this.value = this.value.slice(0, start) + text + this.value.slice(end);
                this.$nextTick(() => {
                    el.focus();
                    el.setSelectionRange(start + text.length, start + text.length);
                });
            },
        }));
    });
}
