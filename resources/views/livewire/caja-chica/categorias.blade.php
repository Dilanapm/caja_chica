<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto space-y-6">

    {{-- Formulario --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $categoriaId ? 'Editar categoría' : 'Nueva categoría' }}</h3>
        </div>
        <div class="p-5">
            <form wire:submit.prevent="save" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="nombre" value="Nombre" />
                    <x-text-input id="nombre" type="text" class="mt-1 block w-full" wire:model.defer="nombre" />
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Nombre corto para identificar la categoría.</p>
                    <x-input-error class="mt-1" :messages="$errors->get('nombre')" />
                </div>

                <div>
                    <x-input-label for="descripcion" value="Descripción" />
                    <x-text-input id="descripcion" type="text" class="mt-1 block w-full" wire:model.defer="descripcion" placeholder="Opcional" />
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Descripción del tipo de gastos que incluye.</p>
                    <x-input-error class="mt-1" :messages="$errors->get('descripcion')" />
                </div>

                <div class="flex flex-col justify-start pt-1">
                    <x-input-label value="Estado" />
                    <label class="inline-flex items-center gap-2 cursor-pointer mt-2">
                        <input type="checkbox" class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-indigo-600 shadow-sm focus:ring-indigo-500" wire:model.defer="activo">
                        <span class="text-sm text-slate-700 dark:text-slate-300">Activo</span>
                    </label>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Las categorías inactivas no aparecerán al registrar gastos.</p>
                </div>

                <div class="sm:col-span-3 flex items-center gap-3 pt-1">
                    <x-primary-button>
                        {{ $categoriaId ? 'Actualizar' : 'Crear categoría' }}
                    </x-primary-button>
                    @if ($categoriaId)
                        <x-secondary-button type="button" wire:click="cancel">Cancelar</x-secondary-button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Listado --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Categorías</h3>
        </div>

        {{-- Mobile: tarjetas --}}
        <div class="sm:hidden divide-y divide-slate-50 dark:divide-slate-700">
            @forelse($categorias as $categoria)
                <div class="px-5 py-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $categoria->nombre }}</p>
                            @if($categoria->descripcion)
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">{{ $categoria->descripcion }}</p>
                            @endif
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $categoria->activo ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400' }}">
                            {{ $categoria->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-4 mt-2">
                        <button type="button" class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium" wire:click="edit({{ $categoria->id }})">Editar</button>
                        <button type="button" class="text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 font-medium" wire:click="toggleActivo({{ $categoria->id }})">
                            {{ $categoria->activo ? 'Desactivar' : 'Activar' }}
                        </button>
                        @php
                            $msgMobile = $categoria->gastos_count > 0
                                ? "Esta categoría tiene {$categoria->gastos_count} gasto(s) asociado(s) que también serán eliminados. Esta acción no se puede deshacer."
                                : "¿Deseas eliminar esta categoría? Esta acción no se puede deshacer.";
                        @endphp
                        <button type="button" class="text-xs text-red-500 hover:text-red-700 font-medium"
                            @click="$store.confirm.ask('Eliminar categoría', @js($msgMobile), () => $wire.delete({{ $categoria->id }}))">
                            Eliminar
                        </button>
                    </div>
                </div>
            @empty
                <div class="px-5 py-8 text-center text-sm text-slate-400 dark:text-slate-500">Sin categorías.</div>
            @endforelse
        </div>

        {{-- Desktop: tabla --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-700">
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Estado</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Descripción</th>
                        <th class="px-5 py-3 text-right text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                    @forelse($categorias as $categoria)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                            <td class="px-5 py-3 text-sm font-medium text-slate-800 dark:text-slate-100">{{ $categoria->nombre }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $categoria->activo ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400' }}">
                                    {{ $categoria->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-sm text-slate-500 dark:text-slate-400">{{ $categoria->descripcion ?? '—' }}</td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <button type="button" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3" wire:click="edit({{ $categoria->id }})">Editar</button>
                                <button type="button" class="text-xs font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 mr-3" wire:click="toggleActivo({{ $categoria->id }})">
                                    {{ $categoria->activo ? 'Desactivar' : 'Activar' }}
                                </button>
                                @php
                                    $msgDesktop = $categoria->gastos_count > 0
                                        ? "Esta categoría tiene {$categoria->gastos_count} gasto(s) asociado(s) que también serán eliminados. Esta acción no se puede deshacer."
                                        : "¿Deseas eliminar esta categoría? Esta acción no se puede deshacer.";
                                @endphp
                                <button type="button" class="text-xs font-medium text-red-500 hover:text-red-700"
                                    @click="$store.confirm.ask('Eliminar categoría', @js($msgDesktop), () => $wire.delete({{ $categoria->id }}))">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-sm text-slate-400 dark:text-slate-500">Sin categorías.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
