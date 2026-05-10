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
        $userId = (int) auth()->id();

        $saldoTotal = (float) Ingreso::query()->where('user_id', $userId)->sum('monto')
            - (float) Gasto::query()->where('user_id', $userId)->sum('monto');

        $aportantesClave = Aportante::query()
            ->where('user_id', $userId)
            ->whereIn('nombre', ['Reina Marino Marca', 'Fermin Apolaca Marca'])
            ->get()
            ->keyBy('nombre');

        $saldoReina = 0.0;
        if ($aportantesClave->has('Reina Marino Marca')) {
            $aportanteId = (int) $aportantesClave->get('Reina Marino Marca')->id;
            $saldoReina = (float) Ingreso::query()->where('user_id', $userId)->where('aportante_id', $aportanteId)->sum('monto')
                - (float) Gasto::query()->where('user_id', $userId)->where('aportante_id', $aportanteId)->sum('monto');
        }

        $saldoFermin = 0.0;
        if ($aportantesClave->has('Fermin Apolaca Marca')) {
            $aportanteId = (int) $aportantesClave->get('Fermin Apolaca Marca')->id;
            $saldoFermin = (float) Ingreso::query()->where('user_id', $userId)->where('aportante_id', $aportanteId)->sum('monto')
                - (float) Gasto::query()->where('user_id', $userId)->where('aportante_id', $aportanteId)->sum('monto');
        }

        $inicioMes = Carbon::now()->startOfMonth()->toDateString();
        $finMes = Carbon::now()->endOfMonth()->toDateString();

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
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $ultimosGastos = Gasto::query()
            ->with(['aportante', 'categoria'])
            ->where('user_id', $userId)
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $ultimosMovimientos = collect()
            ->merge($ultimosIngresos->map(fn (Ingreso $i) => [
                'tipo' => 'INGRESO',
                'fecha' => $i->fecha,
                'aportante' => $i->aportante?->nombre,
                'categoria' => null,
                'metodo' => $i->metodo_ingreso,
                'monto' => (float) $i->monto,
                'referencia' => $i->referencia,
            ]))
            ->merge($ultimosGastos->map(fn (Gasto $g) => [
                'tipo' => 'GASTO',
                'fecha' => $g->fecha,
                'aportante' => $g->aportante?->nombre,
                'categoria' => $g->categoria?->nombre,
                'metodo' => $g->metodo_pago,
                'monto' => (float) $g->monto,
                'referencia' => $g->referencia,
            ]))
            ->sortByDesc(fn (array $m) => ($m['fecha']?->format('Y-m-d') ?? ''))
            ->take(10)
            ->values();

        return view('livewire.caja-chica.dashboard', [
            'saldoTotal' => $saldoTotal,
            'saldoReina' => $saldoReina,
            'saldoFermin' => $saldoFermin,
            'gastosMesPorCategoria' => $gastosMesPorCategoria,
            'ultimosMovimientos' => $ultimosMovimientos,
        ])->layout('layouts.app', [
            'header' => new HtmlString('<h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>'),
        ]);
    }
}
