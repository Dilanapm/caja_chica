<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('aportantes', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->index();
        });

        Schema::table('categorias_gasto', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->index();
        });

        Schema::table('ingresos', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->index();
        });

        Schema::table('gastos', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->index();
        });

        // Backfill: si ya existen datos, asignarlos al primer usuario creado.
        // (No es posible inferir el dueño real porque antes no se guardaba.)
        $fallbackUserId = DB::table('users')->orderBy('id')->value('id');

        if ($fallbackUserId) {
            DB::table('aportantes')->whereNull('user_id')->update(['user_id' => $fallbackUserId]);
            DB::table('categorias_gasto')->whereNull('user_id')->update(['user_id' => $fallbackUserId]);
            DB::table('ingresos')->whereNull('user_id')->update(['user_id' => $fallbackUserId]);
            DB::table('gastos')->whereNull('user_id')->update(['user_id' => $fallbackUserId]);
        }

        // Unicidad por usuario (permite que distintos usuarios repitan nombres).
        Schema::table('aportantes', function (Blueprint $table) {
            $table->dropUnique(['nombre']);
            $table->unique(['user_id', 'nombre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aportantes', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'nombre']);
            $table->unique('nombre');
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('categorias_gasto', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('ingresos', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('gastos', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
