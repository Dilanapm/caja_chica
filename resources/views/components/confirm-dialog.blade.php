{{-- Modal de confirmación global — activado con $store.confirm.ask(title, message, callback) --}}
<div
    x-data
    x-cloak
    x-show="$store.confirm.open"
    x-transition:enter="ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true"
    @keydown.escape.window="$store.confirm.cancel()"
>
    {{-- Backdrop --}}
    <div
        class="absolute inset-0 bg-black/30 dark:bg-black/50 backdrop-blur-sm"
        @click="$store.confirm.cancel()"
    ></div>

    {{-- Panel --}}
    <div
        x-show="$store.confirm.open"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-sm mx-auto p-6"
        @click.stop
    >
        {{-- Ícono de advertencia --}}
        <div class="flex items-center justify-center w-11 h-11 rounded-full bg-red-50 dark:bg-red-900/30 mx-auto mb-4">
            <svg class="w-5 h-5 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z" />
            </svg>
        </div>

        <h3
            class="text-center text-base font-semibold text-slate-800 dark:text-slate-100"
            x-text="$store.confirm.title"
        ></h3>

        <p
            class="mt-2 text-center text-sm text-slate-500 dark:text-slate-400 leading-relaxed"
            x-text="$store.confirm.message"
        ></p>

        <div class="mt-6 flex flex-col-reverse sm:flex-row gap-2 sm:gap-3">
            <button
                type="button"
                @click="$store.confirm.cancel()"
                class="flex-1 px-4 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-xl transition"
            >
                Cancelar
            </button>
            <button
                type="button"
                @click="$store.confirm.execute()"
                class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-xl transition"
            >
                Confirmar
            </button>
        </div>
    </div>
</div>
