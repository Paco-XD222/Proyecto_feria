<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();

            // CLAVE FORÁNEA: Vincula el pago al afiliado.
            // Asume que la tabla de afiliados se llama 'afiliados' y el id es 'id_afiliado' si no es 'id'.
            // Si tu clave principal de afiliados se llama solo 'id', puedes usar: ->constrained();
            $table->unsignedBigInteger('afiliado_id');
            $table->foreign('afiliado_id')->references('id_afiliado')->on('afiliados')->onDelete('cascade');

            // DATOS DEL PAGO
            $table->string('concepto', 100);    // Ej: 'Pago de Afiliación Inicial', 'Recarnetización'
            $table->decimal('monto', 8, 2);    // Monto pagado (Ej: 150.00)
            $table->date('fecha_pago');        // La fecha en que se realizó el pago

            // OPCIONALES DE TRAZABILIDAD
            $table->string('nro_recibo', 50)->nullable(); // Número de recibo manual (puede ser nulo)

            // DATOS DE GESTIÓN INTERNA
            // Útil para saber qué directivo registró el pago
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};