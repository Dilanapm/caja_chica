<?php

namespace Database\Seeders;

use App\Models\CategoriaGasto;
use Illuminate\Database\Seeder;

class CategoriaGastoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Internet'],
            ['nombre' => 'Luz'],
            ['nombre' => 'Pasajes'],
        ];

        foreach ($categorias as $categoria) {
            CategoriaGasto::updateOrCreate(
                ['nombre' => $categoria['nombre']],
                ['activo' => true]
            );
        }
    }
}
