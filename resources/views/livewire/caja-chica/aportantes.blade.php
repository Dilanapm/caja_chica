<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto space-y-6">

    {{-- Formulario --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $aportanteId ? 'Editar aportante' : 'Nuevo aportante' }}</h3>
        </div>
        <div class="p-5">
            <form wire:submit.prevent="save" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="nombre" value="Nombre" />
                    <x-text-input id="nombre" type="text" class="mt-1 block w-full" wire:model.defer="nombre" />
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Nombre completo del aportante.</p>
                    <x-input-error class="mt-1" :messages="$errors->get('nombre')" />
                </div>

                <div>
                    <x-input-label for="nota" value="Nota" />
                    <x-text-input id="nota" type="text" class="mt-1 block w-full" wire:model.defer="nota" placeholder="Opcional" />
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Observaciones o información adicional.</p>
                    <x-input-error class="mt-1" :messages="$errors->get('nota')" />
                </div>

                <div class="flex flex-col justify-start pt-1">
                    <x-input-label value="Estado" />
                    <label class="inline-flex items-center gap-2 cursor-pointer mt-2">
                        <input type="checkbox" class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-indigo-600 shadow-sm focus:ring-indigo-500" wire:model.defer="activo">
                        <span class="text-sm text-slate-700 dark:text-slate-300">Activo</span>
                    </label>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Los aportantes inactivos no aparecerán en nuevos registros.</p>
                </div>

                <div class="sm:col-span-3 flex items-center gap-3 pt-1">
                    <x-primary-button>
                        {{ $aportanteId ? 'Actualizar' : 'Crear aportante' }}
                    </x-primary-button>
                    @if ($aportanteId)
                        <x-secondary-button type="button" wire:click="cancel">Cancelar</x-secondary-button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Listado --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Aportantes</h3>
        </div>

        {{-- Mobile: tarjetas --}}
        <div class="sm:hidden divide-y divide-slate-50 dark:divide-slate-700">
            @forelse($aportantes as $aportante)
                <div class="px-5 py-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $aportante->nombre }}</p>
                            @if($aportante->nota)
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">{{ $aportante->nota }}</p>
                            @endif
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $aportante->activo ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400' }}">
                            {{ $aportante->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-4 mt-2">
                        <button type="button" class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium" wire:click="edit({{ $aportante->id }})">Editar</button>
                        <button type="button" class="text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 font-medium" wire:click="toggleActivo({{ $aportante->id }})">
                            {{ $aportante->activo ? 'Desactivar' : 'Activar' }}
                        </button>
                    </div>
                </div>
            @empty
                <div class="px-5 py-8 text-center text-sm text-slate-400 dark:text-slate-500">Sin aportantes.</div>
            @endforelse
        </div>

        {{-- Desktop: tabla --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-700">
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Nombre</th>
                        @if($isAdmin)
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Usuario</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Saldo</th>
                        @endif
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Estado</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Nota</th>
                        <th class="px-5 py-3 text-right text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                    @forelse($aportantes as $aportante)
                        @php
                            $saldo = (float)($aportante->total_ingresos ?? 0) - (float)($aportante->total_gastos ?? 0);
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                            <td class="px-5 py-3 text-sm font-medium text-slate-800 dark:text-slate-100">{{ $aportante->nombre }}</td>
                            @if($isAdmin)
                                <td class="px-5 py-3 text-sm text-slate-500 dark:text-slate-400">
                                    {{ $aportante->user?->name ?? '—' }}
                                    <div class="text-xs text-slate-400">{{ $aportante->user?->email }}</div>
                                </td>
                                <td class="px-5 py-3 text-sm text-right font-medium {{ $saldo >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">
                                    Bs {{ number_format($saldo, 2, '.', ',') }}
                                </td>
                            @endif
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $aportante->activo ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400' }}">
                                    {{ $aportante->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-sm text-slate-500 dark:text-slate-400">{{ $aportante->nota ?? '—' }}</td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <button type="button" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3" wire:click="edit({{ $aportante->id }})">Editar</button>
                                <button type="button" class="text-xs font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200" wire:click="toggleActivo({{ $aportante->id }})">
                                    {{ $aportante->activo ? 'Desactivar' : 'Activar' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 6 : 4 }}" class="px-5 py-8 text-center text-sm text-slate-400 dark:text-slate-500">Sin aportantes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
