<?php

namespace App\Livewire\CajaChica;

use App\Models\Aportante;
use App\Models\Gasto;
use App\Models\Ingreso;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Reportes extends Component
{
    public string $desde;
    public string $hasta;
    public ?int $aportante_id = null;

    public function mount(): void
    {
        $this->desde = Carbon::now()->startOfMonth()->toDateString();
        $this->hasta = Carbon::now()->toDateString();
    }

    public function generarPdf()
    {
        $userId = (int) auth()->id();

        $rateKey = 'pdf:'.(auth()->id() ? ('user:'.auth()->id()) : ('ip:'.(request()->ip() ?? 'unknown')));
        $maxAttempts = 10;
        $decaySeconds = 60 * 10;

        if (RateLimiter::tooManyAttempts($rateKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($rateKey);
            $this->addError('pdf', 'Demasiadas solicitudes de PDF. Intenta nuevamente en '.$seconds.' segundos.');
            return;
        }

        RateLimiter::hit($rateKey, $decaySeconds);

        $data = $this->validate([
            'desde' => ['required', 'date'],
            'hasta' => ['required', 'date', 'after_or_equal:desde'],
            'aportante_id' => [
                'nullable',
                'integer',
                Rule::exists('aportantes', 'id')->where(fn ($q) => $q->where('user_id', $userId)),
            ],
        ]);

        $desde = Carbon::parse($data['desde'])->toDateString();
        $hasta = Carbon::parse($data['hasta'])->toDateString();
        $aportanteId = $data['aportante_id'] ? (int) $data['aportante_id'] : null;

        $ingresosQuery = Ingreso::query()
            ->with('aportante')
            ->where('user_id', $userId)
            ->whereBetween('fecha', [$desde, $hasta])
            ->orderBy('fecha')
            ->orderBy('id');

        $gastosQuery = Gasto::query()
            ->with(['aportante', 'categoria'])
            ->where('user_id', $userId)
            ->whereBetween('fecha', [$desde, $hasta])
            ->orderBy('fecha')
            ->orderBy('id');

        if ($aportanteId) {
            $ingresosQuery->where('aportante_id', $aportanteId);
            $gastosQuery->where('aportante_id', $aportanteId);
        }

        $ingresos = $ingresosQuery->get();
        $gastos = $gastosQuery->get();

        $ingresosTotales = Ingreso::query()
            ->select('aportante_id', DB::raw('SUM(monto) as total'))
            ->where('user_id', $userId)
            ->whereBetween('fecha', [$desde, $hasta])
            ->when($aportanteId, fn ($q) => $q->where('aportante_id', $aportanteId))
            ->groupBy('aportante_id')
            ->pluck('total', 'aportante_id');

        $gastosTotales = Gasto::query()
            ->select('aportante_id', DB::raw('SUM(monto) as total'))
            ->where('user_id', $userId)
            ->whereBetween('fecha', [$desde, $hasta])
            ->when($aportanteId, fn ($q) => $q->where('aportante_id', $aportanteId))
            ->groupBy('aportante_id')
            ->pluck('total', 'aportante_id');

        $aportantes = Aportante::query()
            ->where('user_id', $userId)
            ->when($aportanteId, fn ($q) => $q->where('id', $aportanteId))
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $resumenPorAportante = $aportantes->map(function (Aportante $a) use ($ingresosTotales, $gastosTotales) {
            $totalAportado = (float) ($ingresosTotales[$a->id] ?? 0);
            $totalGastado = (float) ($gastosTotales[$a->id] ?? 0);

            return [
                'nombre' => $a->nombre,
                'total_aportado' => $totalAportado,
                'total_gastado' => $totalGastado,
                'saldo' => $totalAportado - $totalGastado,
            ];
        });

        $totalIngresos = (float) $ingresos->sum(fn (Ingreso $i) => (float) $i->monto);
        $totalGastos = (float) $gastos->sum(fn (Gasto $g) => (float) $g->monto);
        $saldoTotal = $totalIngresos - $totalGastos;

        $pdf = Pdf::loadView('reports.caja-chica', [
            'desde' => $desde,
            'hasta' => $hasta,
            'emitidoEn' => Carbon::now(),
            'resumenPorAportante' => $resumenPorAportante,
            'ingresos' => $ingresos,
            'gastos' => $gastos,
            'totalIngresos' => $totalIngresos,
            'totalGastos' => $totalGastos,
            'saldoTotal' => $saldoTotal,
        ])->setPaper('a4', 'portrait');

        $filename = 'reporte-caja-chica-'.$desde.'-a-'.$hasta.'.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    public function render()
    {
        $userId = (int) auth()->id();

        return view('livewire.caja-chica.reportes', [
            'aportantes' => Aportante::query()
                ->where('user_id', $userId)
                ->orderBy('nombre')
                ->get(['id', 'nombre']),
        ])->layout('layouts.app', [
            'header' => new HtmlString('<h2 class="font-semibold text-xl text-gray-800 leading-tight">Reportes</h2>'),
        ]);
    }
}
