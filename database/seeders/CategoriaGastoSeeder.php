<?php

namespace Database\Seeders;

use App\Models\CategoriaGasto;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategoriaGastoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::query()->pluck('id');

        if ($userIds->isEmpty()) {
            return;
        }

        $categorias = [
            ['nombre' => 'Internet'],
            ['nombre' => 'Luz'],
            ['nombre' => 'Pasajes'],
        ];

        foreach ($userIds as $userId) {
            foreach ($categorias as $categoria) {
                CategoriaGasto::updateOrCreate(
                    ['user_id' => $userId, 'nombre' => $categoria['nombre']],
                    ['activo' => true]
                );
            }
        }
    }
}
