<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  // En database/migrations/..._create_puestos_table.php

public function up(): void
{
    Schema::create('puestos', function (Blueprint $table) {
        $table->id('id_puesto');
        
        // 1. Identificador del Puesto (tu "nro de Kardex" - el ID del puesto)
        $table->string('nro_kardex', 10)->unique(); // Ej: 101, 205 (Antes numero_puesto)
        
        // 2. Ubicación Estructural
        $table->string('ubicacion_venta', 100);     // Ej: "Pasillo Central A"
        $table->string('fila', 10);                 // Ej: "A", "B", "C" (más corto)
        
        // 3. Número de Libro (tu "nro de Kardex" - el sector)
        $table->string('nro_libro', 20)->nullable(); // Ej: 1, 2, 3 (Antes no_kardex)

        // 4. Medida
       $table->decimal('medida_puesto', 8, 2)->nullable();
        
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puestos');
    }
};
