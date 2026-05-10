<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingreso extends Model
{
    public const METODO_EFECTIVO = 'EFECTIVO';
    public const METODO_QR = 'QR';

    public const METODOS = [
        self::METODO_EFECTIVO,
        self::METODO_QR,
    ];

    protected $fillable = [
        'fecha',
        'aportante_id',
        'monto',
        'metodo_ingreso',
        'referencia',
        'nota',
        'comprobante_path',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];

    public function aportante(): BelongsTo
    {
        return $this->belongsTo(Aportante::class);
    }
}
