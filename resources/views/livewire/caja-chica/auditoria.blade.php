<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

        {{-- Filtros --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Desde</label>
                    <input type="date" wire:model.live="fDesde"
                        class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Hasta</label>
                    <input type="date" wire:model.live="fHasta"
                        class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Usuario</label>
                    <select wire:model.live="fUsuarioId"
                        class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todos</option>
                        @foreach ($usuarios as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Acción</label>
                    <select wire:model.live="fEvento"
                        class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todas</option>
                        @foreach ($eventos as $clave => $etiqueta)
                            <option value="{{ $clave }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-3 flex justify-end">
                <button wire:click="$set('fDesde', null); $set('fHasta', null); $set('fUsuarioId', null); $set('fEvento', null)"
                    class="text-xs text-indigo-600 hover:underline">
                    Limpiar filtros
                </button>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-left">Fecha / Hora</th>
                            <th class="px-4 py-3 text-left">Usuario</th>
                            <th class="px-4 py-3 text-left">Acción</th>
                            <th class="px-4 py-3 text-left">Modelo</th>
                            <th class="px-4 py-3 text-left">Detalle</th>
                            <th class="px-4 py-3 text-left">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($registros as $r)
                            <tr class="hover:bg-gray-50 transition">
                                {{-- Fecha --}}
                                <td class="px-4 py-3 whitespace-nowrap text-gray-500">
                                    <div>{{ $r->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $r->created_at->format('H:i:s') }}</div>
                                </td>

                                {{-- Usuario --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="font-medium text-gray-800">{{ $r->user?->name ?? '—' }}</span>
                                </td>

                                {{-- Acción --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $colores = [
                                            'created' => 'bg-green-100 text-green-700',
                                            'updated' => 'bg-blue-100 text-blue-700',
                                            'deleted' => 'bg-red-100 text-red-700',
                                            'login'   => 'bg-indigo-100 text-indigo-700',
                                            'logout'  => 'bg-gray-100 text-gray-600',
                                        ];
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $colores[$r->event] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $eventos[$r->event] ?? $r->event }}
                                    </span>
                                </td>

                                {{-- Modelo --}}
                                <td class="px-4 py-3 whitespace-nowrap text-gray-600">
                                    @if($r->auditable_type)
                                        <span class="font-medium">{{ $modelos[$r->auditable_type] ?? class_basename($r->auditable_type) }}</span>
                                        <span class="text-xs text-gray-400 ml-1">#{{ $r->auditable_id }}</span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                {{-- Detalle --}}
                                <td class="px-4 py-3 text-gray-600 max-w-xs">
                                    @if($r->event === 'updated' && $r->old_values && $r->new_values)
                                        <ul class="space-y-0.5">
                                            @foreach($r->new_values as $campo => $nuevo)
                                                <li class="text-xs">
                                                    <span class="text-gray-500">{{ $campo }}:</span>
                                                    <span class="text-red-500 line-through">{{ $r->old_values[$campo] ?? '' }}</span>
                                                    <span class="mx-1 text-gray-400">→</span>
                                                    <span class="text-green-600">{{ $nuevo }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @elseif($r->event === 'created' && $r->new_values)
                                        <span class="text-xs text-gray-400 italic">Nuevo registro</span>
                                    @elseif($r->event === 'deleted' && $r->old_values)
                                        <span class="text-xs text-red-500 italic">Registro eliminado</span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                {{-- IP --}}
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-400">
                                    {{ $r->ip_address ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-gray-400 text-sm">
                                    No hay registros de auditoría con los filtros aplicados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($registros->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $registros->links() }}
                </div>
            @endif
        </div>

        {{-- Total --}}
        <div class="text-xs text-gray-400 text-right">
            {{ $registros->total() }} {{ $registros->total() === 1 ? 'registro' : 'registros' }} encontrados
        </div>

    </div>
</div>
