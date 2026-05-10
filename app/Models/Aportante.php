<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aportante extends Model
{
    protected $fillable = [
        'nombre',
        'activo',
        'nota',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function ingresos(): HasMany
    {
        return $this->hasMany(Ingreso::class);
    }

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class);
    }
}
