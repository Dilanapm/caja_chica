<?php

namespace App\Livewire\CajaChica;

use App\Models\Audit;
use App\Models\User;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Livewire\WithPagination;

class Auditoria extends Component
{
    use WithPagination;

    public ?string $fDesde = null;
    public ?string $fHasta = null;
    public ?int $fUsuarioId = null;
    public ?string $fEvento = null;
    public int $perPage = 20;

    protected $paginationTheme = 'tailwind';

    public const EVENTOS = [
        'created' => 'Creó',
        'updated' => 'Editó',
        'deleted' => 'Eliminó',
        'login'   => 'Inició sesión',
        'logout'  => 'Cerró sesión',
    ];

    public const MODELOS = [
        'App\Models\Ingreso'      => 'Ingreso',
        'App\Models\Gasto'        => 'Gasto',
        'App\Models\Aportante'    => 'Aportante',
        'App\Models\CategoriaGasto' => 'Categoría',
    ];

    public function updated(string $property): void
    {
        if ($property !== 'perPage') {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = Audit::query()
            ->with('user')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($this->fDesde) {
            $query->whereDate('created_at', '>=', $this->fDesde);
        }

        if ($this->fHasta) {
            $query->whereDate('created_at', '<=', $this->fHasta);
        }

        if ($this->fUsuarioId) {
            $query->where('user_id', $this->fUsuarioId);
        }

        if ($this->fEvento) {
            $query->where('event', $this->fEvento);
        }

        return view('livewire.caja-chica.auditoria', [
            'registros' => $query->paginate($this->perPage),
            'usuarios'  => User::orderBy('name')->get(['id', 'name']),
            'eventos'   => self::EVENTOS,
            'modelos'   => self::MODELOS,
        ])->layout('layouts.app', [
            'header' => new HtmlString('<h2 class="font-semibold text-xl text-gray-800 leading-tight">Auditoría</h2>'),
        ]);
    }
}
