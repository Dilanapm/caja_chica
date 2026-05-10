<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto">

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Reporte Caja Chica (PDF)</h3>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Selecciona un rango de fechas y opcionalmente un aportante.</p>
        </div>
        <div class="p-5">
            <form wire:submit.prevent="generarPdf" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="desde" value="Desde" />
                    <x-text-input id="desde" type="date" class="mt-1 block w-full" wire:model.defer="desde" />
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Fecha de inicio del periodo a reportar.</p>
                    <x-input-error class="mt-1" :messages="$errors->get('desde')" />
                </div>

                <div>
                    <x-input-label for="hasta" value="Hasta" />
                    <x-text-input id="hasta" type="date" class="mt-1 block w-full" wire:model.defer="hasta" />
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Fecha de fin del periodo a reportar.</p>
                    <x-input-error class="mt-1" :messages="$errors->get('hasta')" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="aportante_id" value="Aportante (opcional)" />
                    <select id="aportante_id" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" wire:model.defer="aportante_id">
                        <option value="">Todos los aportantes</option>
                        @foreach($aportantes as $a)
                            <option value="{{ $a->id }}">{{ $a->nombre }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Filtra el reporte por un aportante específico, o deja en blanco para incluir todos.</p>
                    <x-input-error class="mt-1" :messages="$errors->get('aportante_id')" />
                </div>

                <div class="sm:col-span-2 pt-1">
                    <x-primary-button class="w-full sm:w-auto justify-center">
                        Generar PDF
                    </x-primary-button>
                    <x-input-error class="mt-2" :messages="$errors->get('pdf')" />
                </div>
            </form>
        </div>
    </div>
</div>
