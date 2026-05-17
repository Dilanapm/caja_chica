<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\CajaChica\Aportantes;
use App\Livewire\CajaChica\Auditoria;
use App\Livewire\CajaChica\Categorias;
use App\Livewire\CajaChica\Dashboard;
use App\Livewire\CajaChica\Gastos;
use App\Livewire\CajaChica\Ingresos;
use App\Livewire\CajaChica\Reportes;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/ingresos', Ingresos::class)->name('ingresos');
    Route::get('/gastos', Gastos::class)->name('gastos');
    Route::get('/categorias', Categorias::class)->name('categorias');
    Route::get('/aportantes', Aportantes::class)->name('aportantes');
    Route::get('/reportes', Reportes::class)->name('reportes');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('admin')->group(function () {
        Route::get('/auditoria', Auditoria::class)->name('auditoria');
    });
});

require __DIR__.'/auth.php';
