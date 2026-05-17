<?php

namespace Database\Seeders;

use App\Models\Aportante;
use App\Models\CategoriaGasto;
use App\Models\Gasto;
use App\Models\Ingreso;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuariosTestSeeder extends Seeder
{
    // Nombres de aportantes — algunos se repiten entre usuarios a propósito
    private array $nombresAportantes = [
        'Ana García López',
        'Carlos Mamani Quispe',
        'María Flores Condori',
        'Juan Pérez Huanca',
        'Rosa Choque Tito',
        'Luis Vargas Mendoza',
        'Elena Quispe Mamani',
        'Pedro Condori Flores',
    ];

    private array $categorias = [
        ['nombre' => 'Internet',     'descripcion' => 'Servicio de internet'],
        ['nombre' => 'Luz',          'descripcion' => 'Factura de electricidad'],
        ['nombre' => 'Pasajes',      'descripcion' => 'Transporte y movilidad'],
        ['nombre' => 'Materiales',   'descripcion' => 'Materiales de oficina'],
        ['nombre' => 'Alimentación', 'descripcion' => 'Refrigerios y comidas'],
        ['nombre' => 'Alquiler',     'descripcion' => 'Pago de alquiler'],
        ['nombre' => 'Comunicación', 'descripcion' => 'Teléfono y comunicaciones'],
        ['nombre' => 'Limpieza',     'descripcion' => 'Productos de limpieza'],
    ];

    private array $usuarios = [
        ['name' => 'Sofía Mendoza Arias',    'email' => 'sofia.mendoza@test.com'],
        ['name' => 'Diego Quispe Chávez',    'email' => 'diego.quispe@test.com'],
        ['name' => 'Valentina Cruz López',   'email' => 'valentina.cruz@test.com'],
        ['name' => 'Andrés Mamani Condori',  'email' => 'andres.mamani@test.com'],
        ['name' => 'Camila Flores Ramos',    'email' => 'camila.flores@test.com'],
        ['name' => 'Roberto Vargas Tito',    'email' => 'roberto.vargas@test.com'],
        ['name' => 'Lucía Huanca Pérez',     'email' => 'lucia.huanca@test.com'],
        ['name' => 'Miguel Choque García',   'email' => 'miguel.choque@test.com'],
        ['name' => 'Paola Ríos Mendoza',     'email' => 'paola.rios@test.com'],
        ['name' => 'Fernando Rojas Quispe',  'email' => 'fernando.rojas@test.com'],
        ['name' => 'Isabel Morales Cruz',    'email' => 'isabel.morales@test.com'],
        ['name' => 'Javier Salinas Flores',  'email' => 'javier.salinas@test.com'],
        ['name' => 'Natalia Vega Condori',   'email' => 'natalia.vega@test.com'],
        ['name' => 'Alejandro Poma Chávez',  'email' => 'alejandro.poma@test.com'],
        ['name' => 'Mariana Gutierrez Lima', 'email' => 'mariana.gutierrez@test.com'],
        ['name' => 'Rodrigo Alvarado Tito',  'email' => 'rodrigo.alvarado@test.com'],
        ['name' => 'Claudia Paredes Mamani', 'email' => 'claudia.paredes@test.com'],
        ['name' => 'Ernesto Cáceres Ríos',   'email' => 'ernesto.caceres@test.com'],
        ['name' => 'Patricia Loza Huanca',   'email' => 'patricia.loza@test.com'],
        ['name' => 'Víctor Apaza Vargas',    'email' => 'victor.apaza@test.com'],
    ];

    public function run(): void
    {
        // Crear admin si no existe
        $admin = User::firstOrCreate(
            ['email' => 'admin@cajachica.test'],
            [
                'name'              => 'Administrador',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command?->info('Creando 20 usuarios de prueba...');

        foreach ($this->usuarios as $i => $datos) {
            $user = User::firstOrCreate(
                ['email' => $datos['email']],
                [
                    'name'              => $datos['name'],
                    'password'          => Hash::make('password'),
                    'role'              => 'user',
                    'email_verified_at' => now(),
                ]
            );

            Auth::loginUsingId($user->id);

            // Cada usuario recibe 3 aportantes — algunos nombres se repiten entre usuarios
            $nombresUser = array_slice($this->nombresAportantes, $i % count($this->nombresAportantes), 3);
            if (count($nombresUser) < 3) {
                $nombresUser = array_merge(
                    $nombresUser,
                    array_slice($this->nombresAportantes, 0, 3 - count($nombresUser))
                );
            }

            $aportanteIds = [];
            foreach ($nombresUser as $nombre) {
                $aportante = Aportante::firstOrCreate(
                    ['user_id' => $user->id, 'nombre' => $nombre],
                    ['activo' => true, 'nota' => null]
                );
                $aportanteIds[] = $aportante->id;
            }

            // Crear categorías propias del usuario (3 categorías rotando la lista)
            $categoriasUser = array_slice($this->categorias, $i % count($this->categorias), 3);
            if (count($categoriasUser) < 3) {
                $categoriasUser = array_merge(
                    $categoriasUser,
                    array_slice($this->categorias, 0, 3 - count($categoriasUser))
                );
            }

            $categoriaIds = [];
            foreach ($categoriasUser as $cat) {
                $categoria = CategoriaGasto::firstOrCreate(
                    ['user_id' => $user->id, 'nombre' => $cat['nombre']],
                    ['descripcion' => $cat['descripcion'], 'activo' => true]
                );
                $categoriaIds[] = $categoria->id;
            }

            // Crear ingresos y gastos por cada aportante
            foreach ($aportanteIds as $idx => $aportanteId) {
                // 2 ingresos por aportante
                $montoIngreso1 = 500 + ($i * 50) + ($idx * 100);
                $montoIngreso2 = 300 + ($i * 30) + ($idx * 50);

                Ingreso::firstOrCreate(
                    [
                        'user_id'      => $user->id,
                        'aportante_id' => $aportanteId,
                        'fecha'        => now()->subDays(30 + $idx)->toDateString(),
                        'monto'        => $montoIngreso1,
                    ],
                    [
                        'metodo_ingreso' => Ingreso::METODO_EFECTIVO,
                        'referencia'     => null,
                        'nota'           => 'Aporte inicial',
                    ]
                );

                Ingreso::firstOrCreate(
                    [
                        'user_id'      => $user->id,
                        'aportante_id' => $aportanteId,
                        'fecha'        => now()->subDays(15 + $idx)->toDateString(),
                        'monto'        => $montoIngreso2,
                    ],
                    [
                        'metodo_ingreso' => Ingreso::METODO_QR,
                        'referencia'     => 'REF-' . strtoupper(substr(md5($user->id . $aportanteId), 0, 8)),
                        'nota'           => 'Segundo aporte',
                    ]
                );

                // 1-2 gastos por aportante (solo si hay saldo disponible)
                $categoriaId = $categoriaIds[$idx % count($categoriaIds)];
                $montoGasto  = min(200 + ($i * 20), (int) ($montoIngreso1 * 0.4));

                Gasto::firstOrCreate(
                    [
                        'user_id'      => $user->id,
                        'aportante_id' => $aportanteId,
                        'fecha'        => now()->subDays(10 + $idx)->toDateString(),
                        'monto'        => $montoGasto,
                    ],
                    [
                        'categoria_id' => $categoriaId,
                        'metodo_pago'  => Gasto::METODO_EFECTIVO,
                        'descripcion'  => 'Gasto de prueba ' . ($idx + 1),
                        'proveedor'    => null,
                        'referencia'   => null,
                    ]
                );
            }

            $this->command?->info("  Usuario {$user->email} creado con " . count($aportanteIds) . ' aportantes.');
        }

        Auth::logout();

        $totalUsuarios = count($this->usuarios);
        $this->command?->info("Seeder completado: {$totalUsuarios} usuarios creados (contraseña: password).");
        $this->command?->info('Admin: admin@cajachica.test / password');
    }
}
