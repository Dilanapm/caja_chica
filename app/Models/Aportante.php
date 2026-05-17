<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aportante extends Model
{
    use Auditable;

    protected $fillable = [
        'user_id',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
