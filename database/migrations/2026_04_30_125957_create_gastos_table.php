<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha')->index();
            $table->foreignId('aportante_id')->index()->constrained('aportantes');
            $table->foreignId('categoria_id')->index()->constrained('categorias_gasto');
            $table->decimal('monto', 12, 2);
            $table->string('metodo_pago', 20)->index();
            $table->string('descripcion');
            $table->string('proveedor')->nullable();
            $table->string('referencia')->nullable();
            $table->string('comprobante_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
