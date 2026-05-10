import './bootstrap';

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

    Alpine.store('toast', {
        items: [],
        add(message, type = 'success') {
            const id = Date.now();
            this.items.push({ id, message, type });
            setTimeout(() => this.remove(id), 4000);
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
