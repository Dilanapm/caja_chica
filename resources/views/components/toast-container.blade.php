{{-- Escucha el evento Livewire "toast" y muestra notificaciones flotantes --}}
<div
    x-data
    x-cloak
    @toast.window="$store.toast.add($event.detail.message, $event.detail.type ?? 'success')"
    class="fixed top-4 right-4 z-50 flex flex-col gap-2 w-80 pointer-events-none"
    role="region"
    aria-live="polite"
>
    <template x-for="item in $store.toast.items" :key="item.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-6"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-6"
            :class="{
                'bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-900/30 dark:border-emerald-700 dark:text-emerald-300': item.type === 'success',
                'bg-red-50 border-red-200 text-red-700 dark:bg-red-900/30 dark:border-red-700 dark:text-red-300': item.type === 'error',
                'bg-amber-50 border-amber-200 text-amber-800 dark:bg-amber-900/30 dark:border-amber-700 dark:text-amber-300': item.type === 'warning',
            }"
            class="pointer-events-auto flex items-start gap-3 px-4 py-3 rounded-xl border shadow-lg text-sm"
        >
            {{-- Ícono --}}
            <span class="shrink-0 mt-0.5">
                <template x-if="item.type === 'success'">
                    <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </template>
                <template x-if="item.type === 'error'">
                    <svg class="w-4 h-4 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </template>
                <template x-if="item.type === 'warning'">
                    <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z" />
                    </svg>
                </template>
            </span>

            <span x-text="item.message" class="flex-1 leading-snug"></span>

            <button
                @click="$store.toast.remove(item.id)"
                class="shrink-0 opacity-50 hover:opacity-100 transition mt-0.5"
                aria-label="Cerrar"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>
</div>
