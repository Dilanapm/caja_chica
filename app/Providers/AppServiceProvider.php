<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
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
