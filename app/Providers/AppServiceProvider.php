<?php

namespace App\Providers;

use App\Models\Audit;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
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
