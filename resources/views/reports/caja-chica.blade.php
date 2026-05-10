<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte Caja Chica</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        h1 { font-size: 18px; margin: 0 0 6px 0; }
        h2 { font-size: 14px; margin: 16px 0 6px 0; }
        .meta { font-size: 11px; color: #374151; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px; vertical-align: top; }
        th { background: #f3f4f6; font-weight: 600; }
        .text-right { text-align: right; }
        .totals { margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Reporte Caja Chica</h1>
    <div class="meta">
        <div><strong>Rango:</strong> {{ $desde }} a {{ $hasta }}</div>
        <div><strong>Fecha de emisión:</strong> {{ $emitidoEn->format('Y-m-d H:i') }}</div>
    </div>

    <h2>Resumen por aportante</h2>
    <table>
        <thead>
            <tr>
                <th>Aportante</th>
                <th class="text-right">Total aportado</th>
                <th class="text-right">Total gastado</th>
                <th class="text-right">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($resumenPorAportante as $r)
                <tr>
                    <td>{{ $r['nombre'] }}</td>
                    <td class="text-right">{{ number_format((float) $r['total_aportado'], 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format((float) $r['total_gastado'], 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format((float) $r['saldo'], 2, '.', ',') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Sin datos.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Ingresos</h2>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Aportante</th>
                <th>Método</th>
                <th class="text-right">Monto</th>
                <th>Referencia</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ingresos as $ingreso)
                <tr>
                    <td>{{ $ingreso->fecha?->format('Y-m-d') }}</td>
                    <td>{{ $ingreso->aportante?->nombre }}</td>
                    <td>{{ $ingreso->metodo_ingreso }}</td>
                    <td class="text-right">{{ number_format((float) $ingreso->monto, 2, '.', ',') }}</td>
                    <td>{{ $ingreso->referencia ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Sin ingresos en el rango.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Gastos</h2>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Aportante</th>
                <th>Categoría</th>
                <th>Método</th>
                <th class="text-right">Monto</th>
                <th>Descripción</th>
                <th>Referencia</th>
            </tr>
        </thead>
        <tbody>
            @forelse($gastos as $gasto)
                <tr>
                    <td>{{ $gasto->fecha?->format('Y-m-d') }}</td>
                    <td>{{ $gasto->aportante?->nombre }}</td>
                    <td>{{ $gasto->categoria?->nombre }}</td>
                    <td>{{ $gasto->metodo_pago }}</td>
                    <td class="text-right">{{ number_format((float) $gasto->monto, 2, '.', ',') }}</td>
                    <td>{{ $gasto->descripcion }}</td>
                    <td>{{ $gasto->referencia ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Sin gastos en el rango.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals">
        <h2>Totales generales</h2>
        <table>
            <tbody>
                <tr>
                    <th>Total ingresos</th>
                    <td class="text-right">{{ number_format((float) $totalIngresos, 2, '.', ',') }}</td>
                </tr>
                <tr>
                    <th>Total gastos</th>
                    <td class="text-right">{{ number_format((float) $totalGastos, 2, '.', ',') }}</td>
                </tr>
                <tr>
                    <th>Saldo</th>
                    <td class="text-right">{{ number_format((float) $saldoTotal, 2, '.', ',') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
