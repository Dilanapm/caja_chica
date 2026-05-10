<?php

namespace App\Livewire\CajaChica;

use App\Models\Aportante;
use App\Models\Ingreso;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Ingresos extends Component
{
    use WithFileUploads;
    use WithPagination;

    private const PER_PAGE_OPTIONS = [10, 25, 50];

    public ?int $ingresoId = null;

    public string $fecha;
    public ?int $aportante_id = null;
    public $monto = '';
    public string $metodo_ingreso = Ingreso::METODO_EFECTIVO;
    public ?string $referencia = null;
    public ?string $nota = null;
    public $comprobante = null;
    public ?string $comprobantePathActual = null;

    public ?string $fDesde = null;
    public ?string $fHasta = null;
    public ?int $fAportanteId = null;
    public ?string $fMetodo = null;
    public string $buscar = '';
    public int $perPage = 10;

    protected $paginationTheme = 'tailwind';

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

        if ($property === 'buscar') {
            $search = trim($this->buscar);
            $search = mb_substr($search, 0, 255);

            if ($search !== $this->buscar) {
                $this->buscar = $search;
            }
        }

        if (in_array($property, ['fDesde', 'fHasta', 'fAportanteId', 'fMetodo', 'buscar', 'perPage'], true)) {
            $this->resetPage();
        }
    }

    protected function rules(): array
    {
        return [
            'fecha' => ['required', 'date'],
            'aportante_id' => ['required', 'integer', 'exists:aportantes,id'],
            'monto' => ['required', 'numeric', 'gt:0'],
            'metodo_ingreso' => ['required', Rule::in(Ingreso::METODOS)],
            'referencia' => ['nullable', 'string', 'max:255'],
            'nota' => ['nullable', 'string', 'max:255'],
            'comprobante' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        $payload = [
            'fecha' => $data['fecha'],
            'aportante_id' => $data['aportante_id'],
            'monto' => $data['monto'],
            'metodo_ingreso' => $data['metodo_ingreso'],
            'referencia' => $data['referencia'],
            'nota' => $data['nota'],
        ];

        if ($this->ingresoId) {
            $ingreso = Ingreso::query()->findOrFail($this->ingresoId);
            $oldPath = $ingreso->comprobante_path;

            if ($this->comprobante) {
                $payload['comprobante_path'] = $this->comprobante->store('comprobantes/ingresos', 'public');
            }

            $ingreso->update($payload);

            if (array_key_exists('comprobante_path', $payload) && $oldPath && str_starts_with($oldPath, 'comprobantes/ingresos/')) {
                Storage::disk('public')->delete($oldPath);
            }

            $this->dispatch('toast', message: 'Ingreso actualizado.');
        } else {
            if ($this->comprobante) {
                $payload['comprobante_path'] = $this->comprobante->store('comprobantes/ingresos', 'public');
            }

            Ingreso::query()->create($payload);
            $this->dispatch('toast', message: 'Ingreso creado.');
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $ingreso = Ingreso::query()->findOrFail($id);

        $this->ingresoId = (int) $ingreso->id;
        $this->fecha = $ingreso->fecha->toDateString();
        $this->aportante_id = (int) $ingreso->aportante_id;
        $this->monto = (string) $ingreso->monto;
        $this->metodo_ingreso = (string) $ingreso->metodo_ingreso;
        $this->referencia = $ingreso->referencia;
        $this->nota = $ingreso->nota;
        $this->comprobante = null;
        $this->comprobantePathActual = $ingreso->comprobante_path;

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $ingreso = Ingreso::query()->findOrFail($id);

        if ($ingreso->comprobante_path && str_starts_with($ingreso->comprobante_path, 'comprobantes/ingresos/')) {
            Storage::disk('public')->delete($ingreso->comprobante_path);
        }

        $ingreso->delete();
        $this->dispatch('toast', message: 'Ingreso eliminado.');
    }

    private function resetForm(): void
    {
        $this->ingresoId = null;
        $this->fecha = now()->toDateString();
        $this->aportante_id = null;
        $this->monto = '';
        $this->metodo_ingreso = Ingreso::METODO_EFECTIVO;
        $this->referencia = null;
        $this->nota = null;
        $this->comprobante = null;
        $this->comprobantePathActual = null;

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        $query = Ingreso::query()
            ->with('aportante')
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

        if ($this->fMetodo) {
            $query->where('metodo_ingreso', $this->fMetodo);
        }

        if (trim($this->buscar) !== '') {
            $search = '%'.trim($this->buscar).'%';
            $query->where(function ($q) use ($search) {
                $q->where('referencia', 'like', $search)
                    ->orWhere('nota', 'like', $search);
            });
        }

        return view('livewire.caja-chica.ingresos', [
            'aportantes' => Aportante::query()->orderBy('nombre')->get(),
            'ingresos' => $query->paginate($this->perPage),
            'metodos' => Ingreso::METODOS,
        ])->layout('layouts.app', [
            'header' => new HtmlString('<h2 class="font-semibold text-xl text-gray-800 leading-tight">Ingresos</h2>'),
        ]);
    }
}
