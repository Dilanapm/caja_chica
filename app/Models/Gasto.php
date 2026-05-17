<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gasto extends Model
{
    use Auditable;

    public const METODO_EFECTIVO = 'EFECTIVO';
    public const METODO_QR = 'QR';

    public const METODOS = [
        self::METODO_EFECTIVO,
        self::METODO_QR,
    ];

    protected $fillable = [
        'user_id',
        'fecha',
        'aportante_id',
        'categoria_id',
        'monto',
        'metodo_pago',
        'descripcion',
        'proveedor',
        'referencia',
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

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaGasto::class, 'categoria_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
