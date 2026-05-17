<?php

namespace Database\Seeders;

use App\Models\CategoriaGasto;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategoriaGastoSeeder extends Seeder
{
    private array $categorias = [
        ['nombre' => 'Luz',                  'descripcion' => 'Factura de electricidad'],
        ['nombre' => 'Agua',                 'descripcion' => 'Servicio de agua potable'],
        ['nombre' => 'Gas',                  'descripcion' => 'Gas doméstico o industrial'],
        ['nombre' => 'Combustible',          'descripcion' => 'Gasolina, diésel u otro combustible'],
        ['nombre' => 'Recreos',              'descripcion' => 'Gastos de recreación y esparcimiento'],
        ['nombre' => 'Comida',               'descripcion' => 'Alimentación y refrigerios'],
        ['nombre' => 'Préstamos bancarios',  'descripcion' => 'Cuotas o pagos de préstamos bancarios'],
        ['nombre' => 'Transporte',           'descripcion' => 'Pasajes y movilidad'],
        ['nombre' => 'Wifi',                 'descripcion' => 'Servicio de internet o WiFi'],
        ['nombre' => 'Otros',                'descripcion' => 'Gastos varios no categorizados'],
    ];

    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command?->warn('No hay usuarios. Crea un usuario primero y vuelve a ejecutar este seeder.');
            return;
        }

        foreach ($users as $user) {
            foreach ($this->categorias as $cat) {
                CategoriaGasto::firstOrCreate(
                    ['user_id' => $user->id, 'nombre' => $cat['nombre']],
                    ['descripcion' => $cat['descripcion'], 'activo' => true]
                );
            }
        }

        $this->command?->info('Categorías creadas para ' . $users->count() . ' usuario(s).');
    }
}
