<?php

namespace App\Livewire\CajaChica;

use App\Models\Aportante;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Aportantes extends Component
{
    public ?int $aportanteId = null;

    public string $nombre = '';
    public ?string $nota = null;
    public bool $activo = true;

    protected function rules(): array
    {
        $userId = (int) auth()->id();

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('aportantes', 'nombre')
                    ->where(fn ($q) => $q->where('user_id', $userId))
                    ->ignore($this->aportanteId),
            ],
            'nota' => ['nullable', 'string'],
            'activo' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $userId = (int) auth()->id();
        $data = $this->validate();

        if ($this->aportanteId) {
            $aportante = Aportante::query()
                ->where('user_id', $userId)
                ->findOrFail($this->aportanteId);
            $aportante->update($data);
            $this->dispatch('toast', message: 'Aportante actualizado.');
        } else {
            Aportante::query()->create([
                ...$data,
                'user_id' => $userId,
            ]);
            $this->dispatch('toast', message: 'Aportante creado.');
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $userId = (int) auth()->id();

        $aportante = Aportante::query()
            ->where('user_id', $userId)
            ->findOrFail($id);

        $this->aportanteId = (int) $aportante->id;
        $this->nombre = (string) $aportante->nombre;
        $this->nota = $aportante->nota;
        $this->activo = (bool) $aportante->activo;
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    public function toggleActivo(int $id): void
    {
        $userId = (int) auth()->id();

        $aportante = Aportante::query()
            ->where('user_id', $userId)
            ->findOrFail($id);
        $aportante->update(['activo' => ! (bool) $aportante->activo]);
        $estado = $aportante->fresh()->activo ? 'activado' : 'desactivado';
        $this->dispatch('toast', message: "Aportante {$estado}.");
    }

    private function resetForm(): void
    {
        $this->aportanteId = null;
        $this->nombre = '';
        $this->nota = null;
        $this->activo = true;

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.caja-chica.aportantes', [
            'aportantes' => Aportante::query()
                ->where('user_id', (int) auth()->id())
                ->orderBy('nombre')
                ->get(),
        ])->layout('layouts.app', [
            'header' => new HtmlString('<h2 class="font-semibold text-xl text-gray-800 leading-tight">Aportantes</h2>'),
        ]);
    }
}
