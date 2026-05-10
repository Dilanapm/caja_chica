<?php

namespace Database\Seeders;

use App\Models\Aportante;
use Illuminate\Database\Seeder;

class AportanteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nombres = [
            'Reina Marino Marca',
            'Fermin Apolaca Marca',
        ];

        foreach ($nombres as $nombre) {
            Aportante::updateOrCreate(
                ['nombre' => $nombre],
                ['activo' => true]
            );
        }
    }
}
