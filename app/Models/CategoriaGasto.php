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

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class, 'categoria_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
