<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

    {{-- Tarjetas de saldo --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm p-5">
            <p class="text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Saldo total</p>
            <p class="mt-2 text-2xl font-semibold {{ $saldoTotal >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                {{ number_format($saldoTotal, 2, '.', ',') }}
            </p>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm p-5">
            <p class="text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Reina Marino Marca</p>
            <p class="mt-2 text-2xl font-semibold {{ $saldoReina >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                {{ number_format($saldoReina, 2, '.', ',') }}
            </p>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm p-5">
            <p class="text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Fermin Apolaca Marca</p>
            <p class="mt-2 text-2xl font-semibold {{ $saldoFermin >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                {{ number_format($saldoFermin, 2, '.', ',') }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Gastos del mes por categoría --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Gastos del mes por categoría</h3>
            </div>
            <div class="divide-y divide-slate-50 dark:divide-slate-700">
                @forelse($gastosMesPorCategoria as $row)
                    <div class="flex items-center justify-between px-5 py-3">
                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $row->categoria?->nombre ?? '—' }}</span>
                        <span class="text-sm font-medium text-red-500">{{ number_format((float) $row->total, 2, '.', ',') }}</span>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-slate-400 dark:text-slate-500">Sin gastos este mes.</div>
                @endforelse
            </div>
        </div>

        {{-- Últimos movimientos --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Últimos movimientos</h3>
            </div>

            {{-- Mobile: lista --}}
            <div class="sm:hidden divide-y divide-slate-50 dark:divide-slate-700">
                @forelse($ultimosMovimientos as $m)
                    <div class="flex items-center justify-between px-5 py-3 gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-200 truncate">{{ $m['aportante'] ?? '—' }}</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                {{ $m['fecha']?->format('d/m/Y') }}
                                · {{ $m['tipo'] === 'INGRESO' ? 'Ingreso' : ($m['categoria'] ?? 'Gasto') }}
                            </p>
                        </div>
                        <span class="text-sm font-semibold shrink-0 {{ $m['tipo'] === 'INGRESO' ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ $m['tipo'] === 'INGRESO' ? '+' : '-' }}{{ number_format((float) $m['monto'], 2, '.', ',') }}
                        </span>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-slate-400 dark:text-slate-500">Aún no hay movimientos.</div>
                @endforelse
            </div>

            {{-- Desktop: tabla --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-700">
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Aportante</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Categoría</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Monto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                        @forelse($ultimosMovimientos as $m)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $m['tipo'] === 'INGRESO' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400' }}">
                                        {{ $m['tipo'] === 'INGRESO' ? 'Ingreso' : 'Gasto' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-sm text-slate-600 dark:text-slate-300">{{ $m['fecha']?->format('d/m/Y') }}</td>
                                <td class="px-5 py-3 text-sm text-slate-700 dark:text-slate-200">{{ $m['aportante'] ?? '—' }}</td>
                                <td class="px-5 py-3 text-sm text-slate-500 dark:text-slate-400">{{ $m['categoria'] ?? '—' }}</td>
                                <td class="px-5 py-3 text-sm font-medium text-right {{ $m['tipo'] === 'INGRESO' ? 'text-emerald-600' : 'text-red-500' }}">
                                    {{ $m['tipo'] === 'INGRESO' ? '+' : '-' }}{{ number_format((float) $m['monto'], 2, '.', ',') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-sm text-slate-400 dark:text-slate-500">Aún no hay movimientos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
