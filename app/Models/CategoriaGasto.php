<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoriaGasto extends Model
{
    use Auditable;

    protected $table = 'categorias_gasto';

    protected $fillable = [
        'user_id',
        'nombre',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function getIconoAttribute(): string
    {
        $mapa = [
            'luz'         => '💡',
            'agua'        => '💧',
            'gas'         => '🔥',
            'combustible' => '⛽',
            'recreo'      => '🎉',
            'comida'      => '🍽️',
            'préstamo'    => '🏦',
            'prestamo'    => '🏦',
            'transporte'  => '🚌',
            'wifi'        => '📶',
            'internet'    => '📶',
        ];

        $lower = mb_strtolower($this->nombre);
        foreach ($mapa as $clave => $icono) {
            if (str_contains($lower, $clave)) {
                return $icono;
            }
        }

        return '📦';
    }

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class, 'categoria_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
