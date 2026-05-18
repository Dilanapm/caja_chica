{{-- Diálogo de éxito global — activado con $store.success.show(message) --}}
<div
    x-data
    x-cloak
    x-show="$store.success.open"
    x-transition:enter="ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true"
    @keydown.escape.window="$store.success.close()"
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/30 dark:bg-black/50 backdrop-blur-sm"></div>

    {{-- Panel --}}
    <div
        x-show="$store.success.open"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-xs mx-auto p-7 text-center"
        @click.stop
    >
        {{-- Círculo con palomita --}}
        <div class="flex items-center justify-center w-16 h-16 rounded-full bg-emerald-50 dark:bg-emerald-900/30 mx-auto mb-5">
            <svg class="w-8 h-8 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
        </div>

        <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">¡Listo!</h3>

        <p
            class="mt-2 text-sm text-slate-500 dark:text-slate-400 leading-relaxed"
            x-text="$store.success.message"
        ></p>

        <button
            type="button"
            @click="$store.success.close()"
            class="mt-6 w-full px-4 py-2.5 text-sm font-semibold text-white bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 rounded-xl transition"
        >
            Continuar
        </button>
    </div>
</div>
