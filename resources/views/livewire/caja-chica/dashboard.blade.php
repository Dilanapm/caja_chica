<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

    {{-- Saldo total --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm p-5">
        <p class="text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">
            {{ $isAdmin ? 'Saldo total global' : 'Mi saldo total' }}
        </p>
        <p class="mt-2 text-3xl font-bold {{ $saldoTotal >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
            Bs {{ number_format($saldoTotal, 2, '.', ',') }}
        </p>
    </div>

    {{-- Saldos por aportante --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                {{ $isAdmin ? 'Saldo por aportante (todos los usuarios)' : 'Saldo por aportante' }}
            </h3>
        </div>

        @if($aportantesConSaldo->isEmpty())
            <div class="px-5 py-8 text-center text-sm text-slate-400 dark:text-slate-500">Sin aportantes registrados.</div>
        @else
            {{-- Mobile: tarjetas --}}
            <div class="sm:hidden divide-y divide-slate-50 dark:divide-slate-700">
                @foreach($aportantesConSaldo as $a)
                    <div class="px-5 py-3 flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $a->nombre }}</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500">
                                ↑ Bs {{ number_format($a->total_ingresos, 2, '.', ',') }}
                                · ↓ Bs {{ number_format($a->total_gastos, 2, '.', ',') }}
                            </p>
                        </div>
                        <span class="text-sm font-semibold shrink-0 {{ $a->saldo >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                            Bs {{ number_format($a->saldo, 2, '.', ',') }}
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Desktop: tabla --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-700">
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Aportante</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total ingresos</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total gastos</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                        @foreach($aportantesConSaldo as $a)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                                <td class="px-5 py-3 text-sm font-medium text-slate-800 dark:text-slate-100">{{ $a->nombre }}</td>
                                <td class="px-5 py-3 text-sm text-right text-emerald-600 dark:text-emerald-400">
                                    Bs {{ number_format($a->total_ingresos, 2, '.', ',') }}
                                </td>
                                <td class="px-5 py-3 text-sm text-right text-red-500 dark:text-red-400">
                                    Bs {{ number_format($a->total_gastos, 2, '.', ',') }}
                                </td>
                                <td class="px-5 py-3 text-sm font-semibold text-right {{ $a->saldo >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">
                                    Bs {{ number_format($a->saldo, 2, '.', ',') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
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
                        <span class="text-sm font-medium text-red-500">Bs {{ number_format((float) $row->total, 2, '.', ',') }}</span>
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

            {{-- Mobile --}}
            <div class="sm:hidden divide-y divide-slate-50 dark:divide-slate-700">
                @forelse($ultimosMovimientos as $m)
                    <div class="flex items-center justify-between px-5 py-3 gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-200 truncate">{{ $m['aportante'] ?? '—' }}</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                {{ $m['fecha']?->format('d/m/Y') }}
                                · {{ $m['tipo'] === 'INGRESO' ? 'Ingreso' : ($m['categoria'] ?? 'Gasto') }}
                                @if($isAdmin && $m['usuario']) · {{ $m['usuario'] }} @endif
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

            {{-- Desktop --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-700">
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Aportante</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Categoría</th>
                            @if($isAdmin)
                                <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Registrado por</th>
                            @endif
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
                                <td class="px-5 py-3 text-sm text-slate-600 dark:text-slate-300 whitespace-nowrap">{{ $m['fecha']?->format('d/m/Y') }}</td>
                                <td class="px-5 py-3 text-sm text-slate-700 dark:text-slate-200">{{ $m['aportante'] ?? '—' }}</td>
                                <td class="px-5 py-3 text-sm text-slate-500 dark:text-slate-400">{{ $m['categoria'] ?? '—' }}</td>
                                @if($isAdmin)
                                    <td class="px-5 py-3 text-sm text-slate-500 dark:text-slate-400">{{ $m['usuario'] ?? '—' }}</td>
                                @endif
                                <td class="px-5 py-3 text-sm font-medium text-right {{ $m['tipo'] === 'INGRESO' ? 'text-emerald-600' : 'text-red-500' }}">
                                    {{ $m['tipo'] === 'INGRESO' ? '+' : '-' }}{{ number_format((float) $m['monto'], 2, '.', ',') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isAdmin ? 6 : 5 }}" class="px-5 py-8 text-center text-sm text-slate-400 dark:text-slate-500">Aún no hay movimientos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
