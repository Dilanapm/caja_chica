<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email    = env('ADMIN_EMAIL', 'admin@cajachica.local');
        $name     = env('ADMIN_NAME', 'Administrador');
        $password = env('ADMIN_PASSWORD');

        if (! $password) {
            $this->command?->error('Define ADMIN_PASSWORD en el .env antes de ejecutar este seeder.');
            return;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name'              => $name,
                'password'          => Hash::make($password),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $action = $user->wasRecentlyCreated ? 'creado' : 'actualizado';
        $this->command?->info("Admin {$action}: {$email}");
    }
}
