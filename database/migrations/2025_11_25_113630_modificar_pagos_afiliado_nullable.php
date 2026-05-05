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
        Schema::table('pagos', function (Blueprint $table) {
            // 1. Primero eliminar la restricción de clave foránea existente
            $table->dropForeign(['afiliado_id']);
            
            // 2. Modificar la columna para permitir NULL
            $table->unsignedBigInteger('afiliado_id')->nullable()->change();
            
            // 3. Recrear la clave foránea con onDelete('set null')
            $table->foreign('afiliado_id')
                  ->references('id_afiliado')
                  ->on('afiliados')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            // Revertir los cambios en caso de rollback
            $table->dropForeign(['afiliado_id']);
            $table->unsignedBigInteger('afiliado_id')->nullable(false)->change();
            $table->foreign('afiliado_id')
                  ->references('id_afiliado')
                  ->on('afiliados')
                  ->onDelete('cascade');
        });
    }
};