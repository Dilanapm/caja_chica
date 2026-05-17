<?php

namespace Database\Seeders;

use App\Models\Aportante;
use App\Models\Audit;
use App\Models\CategoriaGasto;
use App\Models\Gasto;
use App\Models\Ingreso;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuditoriaTestSeeder extends Seeder
{
    public function run(): void
    {
        // ── Usuarios ─────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@cajachica.test'],
            [
                'name'              => 'Admin Test',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $usuario = User::firstOrCreate(
            ['email' => 'usuario@cajachica.test'],
            [
                'name'              => 'Usuario Test',
                'password'          => Hash::make('password'),
                'role'              => 'user',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info("Admin:   admin@cajachica.test / password (role: admin)");
        $this->command->info("Usuario: usuario@cajachica.test / password (role: user)");

        // ── Datos base (como admin) ───────────────────────────────
        Auth::loginUsingId($admin->id);

        $reina = Aportante::firstOrCreate(
            ['nombre' => 'Reina Marino Marca'],
            ['activo' => true, 'user_id' => $admin->id]
        );

        $fermin = Aportante::firstOrCreate(
            ['nombre' => 'Fermin Apolaca Marca'],
            ['activo' => true, 'user_id' => $admin->id]
        );

        $catInternet = CategoriaGasto::firstOrCreate(
            ['nombre' => 'Internet'],
            ['activo' => true, 'user_id' => $admin->id]
        );

        $catLuz = CategoriaGasto::firstOrCreate(
            ['nombre' => 'Luz'],
            ['activo' => true, 'user_id' => $admin->id]
        );

        $catPasajes = CategoriaGasto::firstOrCreate(
            ['nombre' => 'Pasajes'],
            ['activo' => true, 'user_id' => $admin->id]
        );

        // ── Ingresos (admin crea) ─────────────────────────────────
        Auth::loginUsingId($admin->id);

        $ingreso1 = Ingreso::create([
            'fecha'          => now()->subDays(15)->toDateString(),
            'aportante_id'   => $reina->id,
            'monto'          => 1500.00,
            'metodo_ingreso' => 'EFECTIVO',
            'referencia'     => 'APO-001',
            'nota'           => 'Aporte mensual',
            'user_id'        => $admin->id,
        ]);

        $ingreso2 = Ingreso::create([
            'fecha'          => now()->subDays(10)->toDateString(),
            'aportante_id'   => $fermin->id,
            'monto'          => 2000.00,
            'metodo_ingreso' => 'QR',
            'referencia'     => 'APO-002',
            'user_id'        => $admin->id,
        ]);

        // ── Gastos (usuario regular crea) ─────────────────────────
        Auth::loginUsingId($usuario->id);

        $gasto1 = Gasto::create([
            'fecha'        => now()->subDays(12)->toDateString(),
            'aportante_id' => $reina->id,
            'categoria_id' => $catInternet->id,
            'monto'        => 120.00,
            'metodo_pago'  => 'QR',
            'descripcion'  => 'Pago servicio internet',
            'proveedor'    => 'Entel',
            'referencia'   => 'FAC-2024-001',
            'user_id'      => $usuario->id,
        ]);

        $gasto2 = Gasto::create([
            'fecha'        => now()->subDays(8)->toDateString(),
            'aportante_id' => $fermin->id,
            'categoria_id' => $catLuz->id,
            'monto'        => 85.50,
            'metodo_pago'  => 'EFECTIVO',
            'descripcion'  => 'Factura luz mes de abril',
            'proveedor'    => 'DELAPAZ',
            'user_id'      => $usuario->id,
        ]);

        $gasto3 = Gasto::create([
            'fecha'        => now()->subDays(3)->toDateString(),
            'aportante_id' => $reina->id,
            'categoria_id' => $catPasajes->id,
            'monto'        => 45.00,
            'metodo_pago'  => 'EFECTIVO',
            'descripcion'  => 'Pasajes reunión',
            'user_id'      => $usuario->id,
        ]);

        // ── Edición (admin edita un ingreso y un gasto) ───────────
        Auth::loginUsingId($admin->id);

        $ingreso1->update(['monto' => 1800.00, 'nota' => 'Aporte mensual (corregido)']);

        $gasto2->update(['monto' => 90.00, 'referencia' => 'FAC-LUZ-042']);

        // ── Edición (usuario edita su propio gasto) ───────────────
        Auth::loginUsingId($usuario->id);

        $gasto3->update(['descripcion' => 'Pasajes reunión de coordinación', 'monto' => 50.00]);

        // ── Eliminación (admin elimina un gasto) ──────────────────
        Auth::loginUsingId($admin->id);

        $gastoEliminar = Gasto::create([
            'fecha'        => now()->subDays(1)->toDateString(),
            'aportante_id' => $fermin->id,
            'categoria_id' => $catPasajes->id,
            'monto'        => 30.00,
            'metodo_pago'  => 'EFECTIVO',
            'descripcion'  => 'Gasto de prueba para eliminar',
            'user_id'      => $admin->id,
        ]);

        $gastoEliminar->delete();

        // ── Sesiones simuladas ────────────────────────────────────
        Audit::recordSession('login', $admin);
        Audit::recordSession('login', $usuario);
        Audit::recordSession('logout', $usuario);
        Audit::recordSession('login', $usuario);
        Audit::recordSession('logout', $admin);
        Audit::recordSession('login', $admin);

        Auth::logout();

        $total = Audit::count();
        $this->command->info("Auditoría: {$total} registros creados.");
        $this->command->newLine();
        $this->command->info("Abre /auditoria con el usuario admin para ver el historial.");
    }
}
