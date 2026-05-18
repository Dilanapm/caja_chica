import './bootstrap';

/**
 * Busca el primer campo con error de validación, hace scroll hacia él
 * y le aplica el destello de advertencia.
 */
function scrollToFirstError() {
    const firstItem = document.querySelector('ul.text-red-600 li');
    if (!firstItem) return;

    const wrapper = firstItem.closest('ul')?.parentElement;
    if (!wrapper) return;

    wrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });

    const field = wrapper.querySelector('input:not([type="hidden"]), select, textarea');
    if (field) {
        field.classList.remove('field-error-flash');
        void field.offsetWidth; // reiniciar animación
        field.classList.add('field-error-flash');
        field.addEventListener('animationend', () => {
            field.classList.remove('field-error-flash');
        }, { once: true });
    }
}

// Registrar el hook de Livewire 3 antes de que inicialice Alpine
document.addEventListener('livewire:init', () => {
    Livewire.hook('commit', ({ succeed }) => {
        succeed(() => {
            requestAnimationFrame(scrollToFirstError);
        });
    });
});

// Alpine es provisto por Livewire 3 — no importar ni iniciar por separado.
document.addEventListener('alpine:init', () => {

    Alpine.store('theme', {
        dark: false,
        init() {
            this.dark = localStorage.theme === 'dark' ||
                (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
        },
        toggle() {
            this.dark = !this.dark;
            localStorage.theme = this.dark ? 'dark' : 'light';
            document.documentElement.classList.toggle('dark', this.dark);
        },
    });

    Alpine.store('success', {
        open: false,
        message: '',
        show(message) {
            this.message = message;
            this.open = true;
        },
        close() {
            this.open = false;
        },
    });

    Alpine.store('toast', {
        items: [],
        add(message, type = 'success') {
            if (type === 'success') {
                Alpine.store('success').show(message);
                return;
            }
            const id = Date.now();
            this.items.push({ id, message, type });
            setTimeout(() => this.remove(id), 5000);
        },
        remove(id) {
            this.items = this.items.filter(i => i.id !== id);
        },
    });

    Alpine.store('confirm', {
        open: false,
        title: '',
        message: '',
        _callback: null,
        ask(title, message, callback) {
            this.title    = title;
            this.message  = message;
            this._callback = callback;
            this.open = true;
        },
        execute() {
            if (this._callback) this._callback();
            this._reset();
        },
        cancel() {
            this._reset();
        },
        _reset() {
            this.open = false;
            this._callback = null;
        },
    });

});
