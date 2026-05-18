<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

    {{-- Formulario (oculto para admin) --}}
    @if(!$isAdmin)
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-2">
                @if($gastoId)
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" /></svg>
                @else
                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                @endif
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $gastoId ? 'Editar gasto' : 'Nuevo gasto' }}</h3>
            </div>
        </div>
        <div class="p-5">
            <form wire:submit.prevent="save" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="fecha" value="Fecha" />
                    <x-text-input id="fecha" type="date" class="mt-1 block w-full" wire:model.defer="fecha" />
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Fecha en que se realizó el gasto.</p>
                    <x-input-error class="mt-1" :messages="$errors->get('fecha')" />
                </div>

                <div>
                    <x-input-label for="aportante_id" value="Pagado con dinero de" />
                    <select id="aportante_id" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" wire:model.defer="aportante_id">
                        <option value="">Seleccione…</option>
                        @foreach($aportantes as $a)
                            <option value="{{ $a->id }}">{{ $a->nombre }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Aportante cuyo saldo se descontará.</p>
                    <x-input-error class="mt-1" :messages="$errors->get('aportante_id')" />
                </div>

                <div>
                    <x-input-label for="categoria_id" value="Categoría" />
                    <select id="categoria_id" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" wire:model.defer="categoria_id">
                        <option value="">Seleccione…</option>
                        @foreach($categorias as $c)
                            <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Clasifica el tipo de gasto.</p>
                    <x-input-error class="mt-1" :messages="$errors->get('categoria_id')" />
                </div>

                <div>
                    <x-input-label for="monto" value="Monto" />
                    <x-text-input id="monto" type="number" step="0.01" min="0" class="mt-1 block w-full" wire:model.defer="monto" placeholder="0.00" />
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Importe pagado en bolivianos.</p>
                    <x-input-error class="mt-1" :messages="$errors->get('monto')" />
                </div>

                <div>
                    <x-input-label for="metodo_pago" value="Método" />
                    <select id="metodo_pago" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" wire:model.defer="metodo_pago">
                        @foreach($metodos as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Efectivo o pago QR.</p>
                    <x-input-error class="mt-1" :messages="$errors->get('metodo_pago')" />
                </div>

                <div>
                    <x-input-label for="descripcion" value="Descripción" />
                    <x-text-input id="descripcion" type="text" class="mt-1 block w-full" wire:model.defer="descripcion" />
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Breve descripción de qué se compró o pagó.</p>
                    <x-input-error class="mt-1" :messages="$errors->get('descripcion')" />
                </div>

                <div>
                    <x-input-label for="proveedor" value="Proveedor" />
                    <x-text-input id="proveedor" type="text" class="mt-1 block w-full" wire:model.defer="proveedor" placeholder="Opcional" />
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Nombre del negocio o persona a quien se pagó.</p>
                    <x-input-error class="mt-1" :messages="$errors->get('proveedor')" />
                </div>

                <div>
                    <x-input-label for="referencia" value="Referencia" />
                    <x-text-input id="referencia" type="text" class="mt-1 block w-full" wire:model.defer="referencia" placeholder="Opcional" />
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Número de factura, recibo u otro código.</p>
                    <x-input-error class="mt-1" :messages="$errors->get('referencia')" />
                </div>

                <div class="sm:col-span-2 lg:col-span-3">
                    <x-input-label for="comprobante" value="Comprobante" />
                    <input id="comprobante" type="file" class="mt-1 block w-full text-sm text-slate-600 dark:text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-400 cursor-pointer" wire:model="comprobante" accept="image/*,application/pdf" />
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Foto o PDF de la factura/recibo (máx. 5 MB).</p>
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
                        {{ $gastoId ? 'Actualizar' : 'Crear gasto' }}
                    </x-primary-button>
                    @if ($gastoId)
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
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.511m-3.182 5.51l-5.511-3.181" /></svg>
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Gastos registrados</h3>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-1.5 mb-3">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" /></svg>
                <span class="text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Filtros</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-6 gap-3">
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
                <x-input-label for="fCategoriaId" value="Categoría" />
                <select id="fCategoriaId" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" wire:model.live="fCategoriaId">
                    <option value="">Todas</option>
                    @foreach($categorias as $c)
                        <option value="{{ $c->id }}">{{ $c->nombre }}</option>
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
            <div>
                <x-input-label for="perPage" value="Por página" />
                <select id="perPage" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" wire:model.live="perPage">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
            </div>
        </div>

        {{-- Mobile: tarjetas --}}
        <div class="sm:hidden divide-y divide-slate-50 dark:divide-slate-700">
            @forelse($gastos as $gasto)
                <div class="px-5 py-4 space-y-1">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-2.5 min-w-0">
                            <span class="mt-0.5 inline-flex items-center justify-center w-7 h-7 rounded-full bg-red-50 dark:bg-red-900/20 shrink-0">
                                <svg class="w-3.5 h-3.5 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" /></svg>
                            </span>
                            <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $gasto->descripcion }}</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5 flex flex-wrap items-center gap-1">
                                <span>{{ $gasto->fecha->format('d/m/Y') }}</span>
                                <span>·</span>
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-violet-50 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-violet-500 dark:bg-violet-400 shrink-0"></span>
                                    {{ $gasto->categoria?->nombre ?? '—' }}
                                </span>
                                <span>·</span>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded {{ $gasto->metodo_pago === 'EFECTIVO' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }} font-medium">{{ $gasto->metodo_pago }}</span>
                            </p>
                            @if($gasto->proveedor)
                                <p class="text-xs text-slate-400 dark:text-slate-500">{{ $gasto->proveedor }}</p>
                            @endif
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-red-500 dark:text-red-400 shrink-0">
                            -Bs {{ number_format((float) $gasto->monto, 2, '.', ',') }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 dark:text-slate-500 pl-9">Aportante: {{ $gasto->aportante?->nombre ?? '—' }}</p>
                    @if($isAdmin)
                        <p class="text-xs text-slate-400 dark:text-slate-500 pl-9">Registrado por: {{ $gasto->user?->name ?? '—' }}</p>
                    @endif
                    <div class="flex items-center gap-4 pt-1 pl-9">
                        @if($gasto->comprobante_path)
                            <a class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300" href="{{ Storage::disk('public')->url($gasto->comprobante_path) }}" target="_blank" rel="noopener noreferrer">Comprobante ↗</a>
                        @endif
                        @if(!$isAdmin)
                            <button type="button" class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium" wire:click="edit({{ $gasto->id }})">Editar</button>
                            <button type="button" class="text-xs text-red-500 hover:text-red-700 font-medium" @click="$store.confirm.ask('Eliminar gasto', '¿Deseas eliminar este gasto? Esta acción no se puede deshacer.', () => $wire.delete({{ $gasto->id }}))">Eliminar</button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-5 py-10 text-center">
                    <svg class="mx-auto w-8 h-8 text-slate-300 dark:text-slate-600 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.511m-3.182 5.51l-5.511-3.181" /></svg>
                    <p class="text-sm text-slate-400 dark:text-slate-500">Sin gastos registrados.</p>
                </div>
            @endforelse
        </div>

        {{-- Desktop: tabla --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-700">
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Descripción</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Categoría</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider hidden lg:table-cell">Aportante</th>
                        @if($isAdmin)
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider hidden lg:table-cell">Registrado por</th>
                        @endif
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Método</th>
                        <th class="px-5 py-3 text-right text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Monto</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider hidden xl:table-cell">Proveedor</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider hidden xl:table-cell">Referencia</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider hidden lg:table-cell">Comprobante</th>
                        <th class="px-5 py-3 text-right text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                    @forelse($gastos as $gasto)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                            <td class="px-5 py-3 text-sm text-slate-600 dark:text-slate-300 whitespace-nowrap">{{ $gasto->fecha->format('d/m/Y') }}</td>
                            <td class="px-5 py-3 text-sm text-slate-800 dark:text-slate-100">{{ $gasto->descripcion }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-violet-50 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-violet-500 dark:bg-violet-400 shrink-0"></span>
                                    {{ $gasto->categoria?->nombre ?? '—' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-sm text-slate-600 dark:text-slate-300 hidden lg:table-cell">{{ $gasto->aportante?->nombre ?? '—' }}</td>
                            @if($isAdmin)
                                <td class="px-5 py-3 text-sm text-slate-500 dark:text-slate-400 hidden lg:table-cell">
                                    {{ $gasto->user?->name ?? '—' }}
                                </td>
                            @endif
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $gasto->metodo_pago === 'EFECTIVO' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                    {{ $gasto->metodo_pago }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-sm font-semibold text-red-500 dark:text-red-400 text-right whitespace-nowrap">
                                -Bs {{ number_format((float) $gasto->monto, 2, '.', ',') }}
                            </td>
                            <td class="px-5 py-3 text-sm text-slate-500 dark:text-slate-400 hidden xl:table-cell">{{ $gasto->proveedor ?? '—' }}</td>
                            <td class="px-5 py-3 text-sm text-slate-500 dark:text-slate-400 hidden xl:table-cell">{{ $gasto->referencia ?? '—' }}</td>
                            <td class="px-5 py-3 text-sm hidden lg:table-cell">
                                @if ($gasto->comprobante_path)
                                    <a class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-xs" href="{{ Storage::disk('public')->url($gasto->comprobante_path) }}" target="_blank" rel="noopener noreferrer">Ver ↗</a>
                                @else
                                    <span class="text-slate-300 dark:text-slate-600">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                @if(!$isAdmin)
                                    <button type="button" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3" wire:click="edit({{ $gasto->id }})">Editar</button>
                                    <button type="button" class="text-xs font-medium text-red-500 hover:text-red-700" @click="$store.confirm.ask('Eliminar gasto', '¿Deseas eliminar este gasto? Esta acción no se puede deshacer.', () => $wire.delete({{ $gasto->id }}))">Eliminar</button>
                                @else
                                    <span class="text-xs text-slate-300 dark:text-slate-600">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 11 : 10 }}" class="px-5 py-10 text-center">
                                <svg class="mx-auto w-8 h-8 text-slate-300 dark:text-slate-600 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.511m-3.182 5.51l-5.511-3.181" /></svg>
                                <p class="text-sm text-slate-400 dark:text-slate-500">Sin gastos registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-700">
            {{ $gastos->links() }}
        </div>
    </div>
</div>
