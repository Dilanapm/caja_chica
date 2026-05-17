<?php

namespace App\Livewire\CajaChica;

use App\Models\CategoriaGasto;
use App\Models\Gasto;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class Categorias extends Component
{
    public ?int $categoriaId = null;

    public string $nombre = '';
    public ?string $descripcion = null;
    public bool $activo = true;

    private function isAdmin(): bool
    {
        return auth()->user()->isAdmin();
    }

    protected function rules(): array
    {
        return [
            'nombre'      => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'activo'      => ['boolean'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->categoriaId) {
            $categoria = CategoriaGasto::query()
                ->when(! $this->isAdmin(), fn ($q) => $q->where('user_id', auth()->id()))
                ->findOrFail($this->categoriaId);
            $categoria->update($data);
            $this->dispatch('toast', message: 'Categoría actualizada.');
        } else {
            CategoriaGasto::query()->create([
                ...$data,
                'user_id' => auth()->id(),
            ]);
            $this->dispatch('toast', message: 'Categoría creada.');
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $categoria = CategoriaGasto::query()
            ->when(! $this->isAdmin(), fn ($q) => $q->where('user_id', auth()->id()))
            ->findOrFail($id);

        $this->categoriaId  = (int) $categoria->id;
        $this->nombre       = (string) $categoria->nombre;
        $this->descripcion  = $categoria->descripcion;
        $this->activo       = (bool) $categoria->activo;
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $categoria = CategoriaGasto::query()
            ->when(! $this->isAdmin(), fn ($q) => $q->where('user_id', auth()->id()))
            ->withCount(['gastos' => fn ($q) => $q->when(
                ! $this->isAdmin(),
                fn ($q2) => $q2->where('user_id', auth()->id())
            )])
            ->findOrFail($id);

        $gastosCount = (int) $categoria->gastos_count;

        $gastosQuery = $categoria->gastos()
            ->when(! $this->isAdmin(), fn ($q) => $q->where('user_id', auth()->id()));

        $gastosQuery->each(function (Gasto $gasto) {
            if ($gasto->comprobante_path && str_starts_with($gasto->comprobante_path, 'comprobantes/gastos/')) {
                Storage::disk('public')->delete($gasto->comprobante_path);
            }
        });

        $gastosQuery->delete();
        $categoria->delete();

        if ($this->categoriaId === $id) {
            $this->resetForm();
        }

        $mensaje = $gastosCount > 0
            ? "Categoría eliminada junto con {$gastosCount} gasto(s) asociado(s)."
            : 'Categoría eliminada.';

        $this->dispatch('toast', message: $mensaje);
    }

    public function toggleActivo(int $id): void
    {
        $categoria = CategoriaGasto::query()
            ->when(! $this->isAdmin(), fn ($q) => $q->where('user_id', auth()->id()))
            ->findOrFail($id);
        $categoria->update(['activo' => ! (bool) $categoria->activo]);
        $estado = $categoria->fresh()->activo ? 'activada' : 'desactivada';
        $this->dispatch('toast', message: "Categoría {$estado}.");
    }

    private function resetForm(): void
    {
        $this->categoriaId = null;
        $this->nombre      = '';
        $this->descripcion = null;
        $this->activo      = true;

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        $categorias = CategoriaGasto::query()
            ->when(! $this->isAdmin(), fn ($q) => $q->where('user_id', auth()->id()))
            ->withCount(['gastos' => fn ($q) => $q->when(
                ! $this->isAdmin(),
                fn ($q2) => $q2->where('user_id', auth()->id())
            )])
            ->when($this->isAdmin(), fn ($q) => $q->with('user'))
            ->orderBy('nombre')
            ->get();

        return view('livewire.caja-chica.categorias', [
            'categorias' => $categorias,
            'isAdmin'    => $this->isAdmin(),
        ])->layout('layouts.app', [
            'header' => new HtmlString('<h2 class="font-semibold text-xl text-gray-800 leading-tight">Categorías</h2>'),
        ]);
    }
}
