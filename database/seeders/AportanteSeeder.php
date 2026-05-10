<?php

namespace Database\Seeders;

use App\Models\Aportante;
use App\Models\User;
use Illuminate\Database\Seeder;

class AportanteSeeder extends Seeder
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

        $nombres = [
            'Reina Marino Marca',
            'Fermin Apolaca Marca',
        ];

        foreach ($userIds as $userId) {
            foreach ($nombres as $nombre) {
                Aportante::updateOrCreate(
                    ['user_id' => $userId, 'nombre' => $nombre],
                    ['activo' => true]
                );
            }
        }
    }
}
