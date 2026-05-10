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
        Schema::create('ingresos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha')->index();
            $table->foreignId('aportante_id')->index()->constrained('aportantes');
            $table->decimal('monto', 12, 2);
            $table->string('metodo_ingreso', 20)->index();
            $table->string('referencia')->nullable();
            $table->string('nota')->nullable();
            $table->string('comprobante_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingresos');
    }
};
