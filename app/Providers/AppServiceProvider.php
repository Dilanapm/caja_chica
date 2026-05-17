<?php

namespace App\Providers;

use App\Models\Audit;
use App\Models\CategoriaGasto;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Registered::class, function (Registered $event) {
            $categorias = [
                ['nombre' => 'Luz',                 'descripcion' => 'Factura de electricidad'],
                ['nombre' => 'Agua',                'descripcion' => 'Servicio de agua potable'],
                ['nombre' => 'Gas',                 'descripcion' => 'Gas doméstico o industrial'],
                ['nombre' => 'Combustible',         'descripcion' => 'Gasolina, diésel u otro combustible'],
                ['nombre' => 'Recreos',             'descripcion' => 'Gastos de recreación y esparcimiento'],
                ['nombre' => 'Comida',              'descripcion' => 'Alimentación y refrigerios'],
                ['nombre' => 'Préstamos bancarios', 'descripcion' => 'Cuotas o pagos de préstamos bancarios'],
                ['nombre' => 'Transporte',          'descripcion' => 'Pasajes y movilidad'],
                ['nombre' => 'Wifi',                'descripcion' => 'Servicio de internet o WiFi'],
                ['nombre' => 'Otros',               'descripcion' => 'Gastos varios no categorizados'],
            ];

            foreach ($categorias as $cat) {
                CategoriaGasto::firstOrCreate(
                    ['user_id' => $event->user->id, 'nombre' => $cat['nombre']],
                    ['descripcion' => $cat['descripcion'], 'activo' => true]
                );
            }
        });

        Event::listen(Login::class, function (Login $event) {
            Audit::recordSession('login', $event->user);
        });

        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                Audit::recordSession('logout', $event->user);
            }
        });

        RateLimiter::for('web', function (Request $request) {
            $ip = $request->ip() ?? 'unknown';

            if ($request->user()) {
                $userId = (int) $request->user()->id;

                return [
                    Limit::perMinute(600)->by('user:'.$userId),
                    Limit::perMinute(600)->by('ip:'.$ip),
                ];
            }

            return [
                Limit::perMinute(120)->by('ip:'.$ip),
            ];
        });
    }
}
