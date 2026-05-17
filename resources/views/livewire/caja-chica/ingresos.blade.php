<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

    {{-- Formulario (oculto para admin) --}}
    @if(!$isAdmin)
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $ingresoId ? 'Editar ingreso' : 'Nuevo ingreso' }}</h3>
        </div>
        <div class="p-5">
            <form wire:submit.prevent="save" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="fecha" value="Fecha" />
                    <x-text-input id="fecha" type="date" class="mt-1 block w-full" wire:model.defer="fecha" />
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Fecha en que se recibió el dinero.</p>
                    <x-input-error class="mt-1" :messages="$errors->get('fecha')" />
                </div>

                <div>
                    <x-input-label for="aportante_id" value="Aportante" />
                    <select id="aportante_id" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" wire:model.defer="aportante_id">
                        <option value="">Seleccione…</option>
                        @foreach($aportantes as $a)
                            <option value="{{ $a->id }}">{{ $a->nombre }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Persona que realizó el aporte.</p>
                    <x-input-error class="mt-1" :messages="$errors->get('aportante_id')" />
                </div>

                <div>
                    <x-input-label for="monto" value="Monto" />
                    <x-text-input id="monto" type="number" step="0.01" min="0" class="mt-1 block w-full" wire:model.defer="monto" placeholder="0.00" />
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Importe recibido en bolivianos.</p>
                    <x-input-error class="mt-1" :messages="$errors->get('monto')" />
                </div>

                <div>
                    <x-input-label for="metodo_ingreso" value="Método" />
                    <select id="metodo_ingreso" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" wire:model.defer="metodo_ingreso">
                        @foreach($metodos as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Efectivo o transferencia QR.</p>
                    <x-input-error class="mt-1" :messages="$errors->get('metodo_ingreso')" />
                </div>

                <div>
                    <x-input-label for="referencia" value="Referencia" />
                    <x-text-input id="referencia" type="text" class="mt-1 block w-full" wire:model.defer="referencia" placeholder="Opcional" />
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Número de transacción u otro código identificador.</p>
                    <x-input-error class="mt-1" :messages="$errors->get('referencia')" />
                </div>

                <div>
                    <x-input-label for="nota" value="Nota" />
                    <x-text-input id="nota" type="text" class="mt-1 block w-full" wire:model.defer="nota" placeholder="Opcional" />
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Cualquier aclaración adicional.</p>
                    <x-input-error class="mt-1" :messages="$errors->get('nota')" />
                </div>

                <div class="sm:col-span-2 lg:col-span-3">
                    <x-input-label for="comprobante" value="Comprobante" />
                    <input id="comprobante" type="file" class="mt-1 block w-full text-sm text-slate-600 dark:text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-400 cursor-pointer" wire:model="comprobante" accept="image/*,application/pdf" />
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Foto o PDF del recibo (máx. 5 MB).</p>
                    <x-input-error class="mt-1" :messages="$errors->get('comprobante')" />

                    @if ($comprobante)
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ $comprobante->getClientOriginalName() }}</p>
                        @if (str($comprobante->getMimeType())->startsWith('image/'))
                            <img src="{{ $comprobante->temporaryUrl() }}" alt="Preview" class="mt-2 h-24 rounded-lg border border-slate-200 dark:border-slate-600 object-cover" />
                        @endif
                    @elseif ($comprobantePathActual)
                        <a class="mt-2 inline-block text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300" href="{{ Storage::disk('public')->url($comprobantePathActual) }}" target="_blank" rel="noopener noreferrer">
                            Ver comprobante actual ↗
                        </a>
                    @endif
                </div>

                <div class="sm:col-span-2 lg:col-span-3 flex items-center gap-3 pt-1">
                    <x-primary-button>
                        {{ $ingresoId ? 'Actualizar' : 'Crear ingreso' }}
                    </x-primary-button>
                    @if ($ingresoId)
                        <x-secondary-button type="button" wire:click="cancel">Cancelar</x-secondary-button>
                    @endif
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Listado --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Listado</h3>
        </div>

        {{-- Filtros --}}
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div>
                <x-input-label for="fDesde" value="Desde" />
                <x-text-input id="fDesde" type="date" class="mt-1 block w-full text-sm" wire:model.live="fDesde" />
            </div>
            <div>
                <x-input-label for="fHasta" value="Hasta" />
                <x-text-input id="fHasta" type="date" class="mt-1 block w-full text-sm" wire:model.live="fHasta" />
            </div>
            <div>
                <x-input-label for="fAportanteId" value="Aportante" />
                <select id="fAportanteId" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" wire:model.live="fAportanteId">
                    <option value="">Todos</option>
                    @foreach($aportantes as $a)
                        <option value="{{ $a->id }}">{{ $a->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label for="fMetodo" value="Método" />
                <select id="fMetodo" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" wire:model.live="fMetodo">
                    <option value="">Todos</option>
                    @foreach($metodos as $m)
                        <option value="{{ $m }}">{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2 sm:col-span-1 lg:col-span-1">
                <x-input-label for="buscar" value="Buscar" />
                <x-text-input id="buscar" type="text" class="mt-1 block w-full text-sm" wire:model.live.debounce.300ms="buscar" placeholder="Referencia o nota…" />
            </div>
            <div>
                <x-input-label for="perPage" value="Por página" />
                <select id="perPage" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" wire:model.live="perPage">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>

        {{-- Mobile: tarjetas --}}
        <div class="sm:hidden divide-y divide-slate-50 dark:divide-slate-700">
            @forelse($ingresos as $ingreso)
                <div class="px-5 py-4 space-y-1">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $ingreso->aportante?->nombre ?? '—' }}</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                {{ $ingreso->fecha->format('d/m/Y') }}
                                · {{ $ingreso->metodo_ingreso }}
                                @if($ingreso->referencia) · {{ $ingreso->referencia }} @endif
                            </p>
                            @if($ingreso->nota)
                                <p class="text-xs text-slate-400 dark:text-slate-500">{{ $ingreso->nota }}</p>
                            @endif
                            @if($isAdmin)
                                <p class="text-xs text-slate-400 dark:text-slate-500">Registrado por: {{ $ingreso->user?->name ?? '—' }}</p>
                            @endif
                        </div>
                        <span class="text-sm font-semibold text-emerald-600 shrink-0">
                            +{{ number_format((float) $ingreso->monto, 2, '.', ',') }}
                        </span>
                    </div>
                    <div class="flex items-center gap-4 pt-1">
                        @if($ingreso->comprobante_path)
                            <a class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300" href="{{ Storage::disk('public')->url($ingreso->comprobante_path) }}" target="_blank" rel="noopener noreferrer">Comprobante ↗</a>
                        @endif
                        @if(!$isAdmin)
                            <button type="button" class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium" wire:click="edit({{ $ingreso->id }})">Editar</button>
                            <button type="button" class="text-xs text-red-500 hover:text-red-700 font-medium" @click="$store.confirm.ask('Eliminar ingreso', '¿Deseas eliminar este ingreso? Esta acción no se puede deshacer.', () => $wire.delete({{ $ingreso->id }}))">Eliminar</button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-5 py-8 text-center text-sm text-slate-400 dark:text-slate-500">Sin ingresos.</div>
            @endforelse
        </div>

        {{-- Desktop: tabla --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-700">
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Aportante</th>
                        @if($isAdmin)
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Registrado por</th>
                        @endif
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Método</th>
                        <th class="px-5 py-3 text-right text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Monto</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider hidden lg:table-cell">Referencia</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider hidden lg:table-cell">Nota</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider hidden lg:table-cell">Comprobante</th>
                        <th class="px-5 py-3 text-right text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                    @forelse($ingresos as $ingreso)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                            <td class="px-5 py-3 text-sm text-slate-600 dark:text-slate-300 whitespace-nowrap">{{ $ingreso->fecha->format('d/m/Y') }}</td>
                            <td class="px-5 py-3 text-sm text-slate-800 dark:text-slate-100">{{ $ingreso->aportante?->nombre ?? '—' }}</td>
                            @if($isAdmin)
                                <td class="px-5 py-3 text-sm text-slate-500 dark:text-slate-400">
                                    {{ $ingreso->user?->name ?? '—' }}
                                </td>
                            @endif
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                    {{ $ingreso->metodo_ingreso }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-sm font-semibold text-emerald-600 text-right whitespace-nowrap">
                                +{{ number_format((float) $ingreso->monto, 2, '.', ',') }}
                            </td>
                            <td class="px-5 py-3 text-sm text-slate-500 dark:text-slate-400 hidden lg:table-cell">{{ $ingreso->referencia ?? '—' }}</td>
                            <td class="px-5 py-3 text-sm text-slate-500 dark:text-slate-400 hidden lg:table-cell">{{ $ingreso->nota ?? '—' }}</td>
                            <td class="px-5 py-3 text-sm hidden lg:table-cell">
                                @if ($ingreso->comprobante_path)
                                    <a class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-xs" href="{{ Storage::disk('public')->url($ingreso->comprobante_path) }}" target="_blank" rel="noopener noreferrer">Ver ↗</a>
                                @else
                                    <span class="text-slate-300 dark:text-slate-600">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                @if(!$isAdmin)
                                    <button type="button" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3" wire:click="edit({{ $ingreso->id }})">Editar</button>
                                    <button type="button" class="text-xs font-medium text-red-500 hover:text-red-700" @click="$store.confirm.ask('Eliminar ingreso', '¿Deseas eliminar este ingreso? Esta acción no se puede deshacer.', () => $wire.delete({{ $ingreso->id }}))">Eliminar</button>
                                @else
                                    <span class="text-xs text-slate-300 dark:text-slate-600">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 9 : 8 }}" class="px-5 py-8 text-center text-sm text-slate-400 dark:text-slate-500">Sin ingresos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-700">
            {{ $ingresos->links() }}
        </div>
    </div>
</div>
