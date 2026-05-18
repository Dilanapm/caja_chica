<?php

namespace App\Livewire\CajaChica;

use App\Models\Aportante;
use App\Models\Gasto;
use App\Models\Ingreso;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $isAdmin = auth()->user()->isAdmin();
        $userId  = (int) auth()->id();

        $inicioMes = Carbon::now()->startOfMonth()->toDateString();
        $finMes    = Carbon::now()->endOfMonth()->toDateString();

        if ($isAdmin) {
            $saldoTotal = (float) Ingreso::query()->sum('monto')
                - (float) Gasto::query()->sum('monto');

            // Group aportantes by nombre, combine saldo across all users
            $aportantesConSaldo = Aportante::query()
                ->withSum('ingresos as total_ingresos', 'monto')
                ->withSum('gastos as total_gastos', 'monto')
                ->get()
                ->groupBy('nombre')
                ->map(fn ($group, $nombre) => (object) [
                    'nombre'         => $nombre,
                    'total_ingresos' => (float) $group->sum('total_ingresos'),
                    'total_gastos'   => (float) $group->sum('total_gastos'),
                    'saldo'          => (float) $group->sum('total_ingresos') - (float) $group->sum('total_gastos'),
                ])
                ->sortByDesc('saldo')
                ->values();

            $gastosMesPorCategoria = Gasto::query()
                ->select('categoria_id', DB::raw('SUM(monto) as total'))
                ->whereBetween('fecha', [$inicioMes, $finMes])
                ->groupBy('categoria_id')
                ->with('categoria')
                ->orderByDesc('total')
                ->get();

            $ultimosIngresos = Ingreso::query()
                ->with(['aportante', 'user'])
                ->orderByDesc('fecha')->orderByDesc('id')
                ->limit(10)->get();

            $ultimosGastos = Gasto::query()
                ->with(['aportante', 'categoria', 'user'])
                ->orderByDesc('fecha')->orderByDesc('id')
                ->limit(10)->get();
        } else {
            $saldoTotal = (float) Ingreso::query()->where('user_id', $userId)->sum('monto')
                - (float) Gasto::query()->where('user_id', $userId)->sum('monto');

            $aportantesConSaldo = Aportante::query()
                ->where('user_id', $userId)
                ->withSum('ingresos as total_ingresos', 'monto')
                ->withSum('gastos as total_gastos', 'monto')
                ->get()
                ->map(fn ($a) => (object) [
                    'nombre'         => $a->nombre,
                    'total_ingresos' => (float) ($a->total_ingresos ?? 0),
                    'total_gastos'   => (float) ($a->total_gastos ?? 0),
                    'saldo'          => (float) ($a->total_ingresos ?? 0) - (float) ($a->total_gastos ?? 0),
                ])
                ->sortByDesc('saldo')
                ->values();

            $gastosMesPorCategoria = Gasto::query()
                ->select('categoria_id', DB::raw('SUM(monto) as total'))
                ->where('user_id', $userId)
                ->whereBetween('fecha', [$inicioMes, $finMes])
                ->groupBy('categoria_id')
                ->with('categoria')
                ->orderByDesc('total')
                ->get();

            $ultimosIngresos = Ingreso::query()
                ->with('aportante')
                ->where('user_id', $userId)
                ->orderByDesc('fecha')->orderByDesc('id')
                ->limit(10)->get();

            $ultimosGastos = Gasto::query()
                ->with(['aportante', 'categoria'])
                ->where('user_id', $userId)
                ->orderByDesc('fecha')->orderByDesc('id')
                ->limit(10)->get();
        }

        $ultimosMovimientos = collect()
            ->merge($ultimosIngresos->map(fn (Ingreso $i) => [
                'tipo'      => 'INGRESO',
                'fecha'     => $i->fecha,
                'aportante' => $i->aportante?->nombre,
                'categoria' => null,
                'metodo'    => $i->metodo_ingreso,
                'monto'     => (float) $i->monto,
                'usuario'   => $i->user?->name ?? null,
            ]))
            ->merge($ultimosGastos->map(fn (Gasto $g) => [
                'tipo'            => 'GASTO',
                'fecha'           => $g->fecha,
                'aportante'       => $g->aportante?->nombre,
                'categoria'       => $g->categoria?->nombre,
                'icono_categoria' => $g->categoria?->icono,
                'metodo'          => $g->metodo_pago,
                'monto'           => (float) $g->monto,
                'usuario'         => $g->user?->name ?? null,
            ]))
            ->sortByDesc(fn (array $m) => ($m['fecha']?->format('Y-m-d') ?? ''))
            ->take(10)
            ->values();

        return view('livewire.caja-chica.dashboard', [
            'isAdmin'               => $isAdmin,
            'saldoTotal'            => $saldoTotal,
            'aportantesConSaldo'    => $aportantesConSaldo,
            'gastosMesPorCategoria' => $gastosMesPorCategoria,
            'ultimosMovimientos'    => $ultimosMovimientos,
        ])->layout('layouts.app', [
            'header' => new HtmlString('<h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>'),
        ]);
    }
}
