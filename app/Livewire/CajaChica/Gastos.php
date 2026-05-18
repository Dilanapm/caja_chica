<?php

namespace App\Livewire\CajaChica;

use App\Models\Aportante;
use App\Models\CategoriaGasto;
use App\Models\Gasto;
use App\Models\Ingreso;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Gastos extends Component
{
    use WithFileUploads;
    use WithPagination;

    private const PER_PAGE_OPTIONS = [10, 25, 50];

    public ?int $gastoId = null;

    public string $fecha;
    public ?int $aportante_id = null;
    public ?int $categoria_id = null;
    public $monto = '';
    public string $metodo_pago = Gasto::METODO_EFECTIVO;
    public string $descripcion = '';
    public ?string $proveedor = null;
    public ?string $referencia = null;
    public $comprobante = null;
    public ?string $comprobantePathActual = null;

    public ?string $fDesde = null;
    public ?string $fHasta = null;
    public ?int $fAportanteId = null;
    public ?int $fCategoriaId = null;
    public ?string $fMetodo = null;
    public int $perPage = 10;

    protected $paginationTheme = 'tailwind';

    private function isAdmin(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function mount(): void
    {
        $this->fecha = now()->toDateString();
    }

    public function updated(string $property): void
    {
        if ($property === 'perPage') {
            $this->perPage = (int) $this->perPage;
            if (! in_array($this->perPage, self::PER_PAGE_OPTIONS, true)) {
                $this->perPage = self::PER_PAGE_OPTIONS[0];
            }
        }

        if (in_array($property, ['fDesde', 'fHasta', 'fAportanteId', 'fCategoriaId', 'fMetodo', 'perPage'], true)) {
            $this->resetPage();
        }
    }

    protected function rules(): array
    {
        return [
            'fecha'        => ['required', 'date'],
            'aportante_id' => [
                'required',
                'integer',
                Rule::exists('aportantes', 'id')->when(
                    ! $this->isAdmin(),
                    fn ($r) => $r->where(fn ($q) => $q->where('user_id', auth()->id()))
                ),
            ],
            'categoria_id' => [
                'required',
                'integer',
                Rule::exists('categorias_gasto', 'id')->when(
                    ! $this->isAdmin(),
                    fn ($r) => $r->where(fn ($q) => $q->where('user_id', auth()->id()))
                ),
            ],
            'monto'        => ['required', 'numeric', 'gt:0'],
            'metodo_pago'  => ['required', Rule::in(Gasto::METODOS)],
            'descripcion'  => ['required', 'string', 'max:255'],
            'proveedor'    => ['nullable', 'string', 'max:255'],
            'referencia'   => ['nullable', 'string', 'max:255'],
            'comprobante'  => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    public function save(): void
    {
        if ($this->isAdmin()) {
            return;
        }

        $data = $this->validate();

        $saldoDisponible = $this->saldoDisponibleParaAportante((int) $data['aportante_id']);
        if ((float) $data['monto'] > $saldoDisponible) {
            $this->addError('monto', 'Saldo insuficiente. Saldo disponible: '.number_format($saldoDisponible, 2, '.', ','));
            return;
        }

        $payload = [
            'user_id'      => auth()->id(),
            'fecha'        => $data['fecha'],
            'aportante_id' => $data['aportante_id'],
            'categoria_id' => $data['categoria_id'],
            'monto'        => $data['monto'],
            'metodo_pago'  => $data['metodo_pago'],
            'descripcion'  => $data['descripcion'],
            'proveedor'    => $data['proveedor'],
            'referencia'   => $data['referencia'],
        ];

        if ($this->gastoId) {
            $gasto   = Gasto::query()
                ->when(! $this->isAdmin(), fn ($q) => $q->where('user_id', auth()->id()))
                ->findOrFail($this->gastoId);
            $oldPath = $gasto->comprobante_path;

            if ($this->comprobante) {
                $payload['comprobante_path'] = $this->comprobante->store('comprobantes/gastos', 'public');
            }

            $gasto->update($payload);

            if (array_key_exists('comprobante_path', $payload) && $oldPath && str_starts_with($oldPath, 'comprobantes/gastos/')) {
                Storage::disk('public')->delete($oldPath);
            }

            $this->dispatch('toast', message: 'Gasto actualizado.');
        } else {
            if ($this->comprobante) {
                $payload['comprobante_path'] = $this->comprobante->store('comprobantes/gastos', 'public');
            }

            Gasto::query()->create($payload);
            $this->dispatch('toast', message: 'Gasto creado.');
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        if ($this->isAdmin()) {
            return;
        }

        $gasto = Gasto::query()
            ->when(! $this->isAdmin(), fn ($q) => $q->where('user_id', auth()->id()))
            ->findOrFail($id);

        $this->gastoId              = (int) $gasto->id;
        $this->fecha                = $gasto->fecha->toDateString();
        $this->aportante_id         = (int) $gasto->aportante_id;
        $this->categoria_id         = (int) $gasto->categoria_id;
        $this->monto                = (string) $gasto->monto;
        $this->metodo_pago          = (string) $gasto->metodo_pago;
        $this->descripcion          = (string) $gasto->descripcion;
        $this->proveedor            = $gasto->proveedor;
        $this->referencia           = $gasto->referencia;
        $this->comprobante          = null;
        $this->comprobantePathActual = $gasto->comprobante_path;

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        if ($this->isAdmin()) {
            return;
        }

        $gasto = Gasto::query()
            ->when(! $this->isAdmin(), fn ($q) => $q->where('user_id', auth()->id()))
            ->findOrFail($id);

        if ($gasto->comprobante_path && str_starts_with($gasto->comprobante_path, 'comprobantes/gastos/')) {
            Storage::disk('public')->delete($gasto->comprobante_path);
        }

        $gasto->delete();
        $this->dispatch('toast', message: 'Gasto eliminado.');
    }

    private function saldoDisponibleParaAportante(int $aportanteId): float
    {
        // Admin calcula sobre todos los registros; usuario normal solo los suyos
        $ingresos = (float) Ingreso::query()
            ->when(! $this->isAdmin(), fn ($q) => $q->where('user_id', auth()->id()))
            ->where('aportante_id', $aportanteId)
            ->sum('monto');

        $gastosQuery = Gasto::query()
            ->when(! $this->isAdmin(), fn ($q) => $q->where('user_id', auth()->id()))
            ->where('aportante_id', $aportanteId);

        if ($this->gastoId) {
            $gastosQuery->where('id', '!=', $this->gastoId);
        }

        return $ingresos - (float) $gastosQuery->sum('monto');
    }

    private function resetForm(): void
    {
        $this->gastoId              = null;
        $this->fecha                = now()->toDateString();
        $this->aportante_id         = null;
        $this->categoria_id         = null;
        $this->monto                = '';
        $this->metodo_pago          = Gasto::METODO_EFECTIVO;
        $this->descripcion          = '';
        $this->proveedor            = null;
        $this->referencia           = null;
        $this->comprobante          = null;
        $this->comprobantePathActual = null;

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        $query = Gasto::query()
            ->with($this->isAdmin() ? ['aportante', 'categoria', 'user'] : ['aportante', 'categoria'])
            ->when(! $this->isAdmin(), fn ($q) => $q->where('user_id', auth()->id()))
            ->orderByDesc('fecha')
            ->orderByDesc('id');

        if ($this->fDesde) {
            $query->whereDate('fecha', '>=', $this->fDesde);
        }
        if ($this->fHasta) {
            $query->whereDate('fecha', '<=', $this->fHasta);
        }
        if ($this->fAportanteId) {
            $query->where('aportante_id', $this->fAportanteId);
        }
        if ($this->fCategoriaId) {
            $query->where('categoria_id', $this->fCategoriaId);
        }
        if ($this->fMetodo) {
            $query->where('metodo_pago', $this->fMetodo);
        }
        return view('livewire.caja-chica.gastos', [
            'aportantes' => Aportante::query()
                ->when(! $this->isAdmin(), fn ($q) => $q->where('user_id', auth()->id()))
                ->orderBy('nombre')
                ->get(),
            'categorias' => CategoriaGasto::query()
                ->when(! $this->isAdmin(), fn ($q) => $q->where('user_id', auth()->id()))
                ->where('activo', true)
                ->orderBy('nombre')
                ->get(),
            'gastos'     => $query->paginate($this->perPage),
            'metodos'    => Gasto::METODOS,
            'isAdmin'    => $this->isAdmin(),
        ])->layout('layouts.app', [
            'header' => new HtmlString('<h2 class="font-semibold text-xl text-gray-800 leading-tight">Gastos</h2>'),
        ]);
    }
}
