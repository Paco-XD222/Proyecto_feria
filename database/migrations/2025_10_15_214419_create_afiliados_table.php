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
       // En la función 'up':
Schema::create('afiliados', function (Blueprint $table) {
    $table->id('id_afiliado');
    $table->string('ci', 20)->unique();
    $table->string('nombre_afiliado', 100);
    $table->string('apellido_paterno', 100);
    $table->string('apellido_materno', 100);
    $table->date('fecha_nacimiento');
    $table->string('direccion', 255);
    $table->string('telefono', 20)->nullable();
    $table->string('estado_civil', 20)->nullable();
    $table->string('nombre_conyuge', 100)->nullable();
    $table->integer('numero_familia')->nullable();

    // DATOS DE AFILIACIÓN Y FKs
    $table->date('fecha_afiliacion');
    $table->string('cargo_alguna_vez', 50)->nullable();
    $table->string('recarnetizacion', 100)->nullable();
    $table->text('observaciones')->nullable();
    $table->text('otros')->nullable();
    
    // RELACIONES (Foreign Keys)
    $table->foreignId('id_gestion')->nullable()->constrained('feria_gestion', 'id_gestion');
    $table->foreignId('id_puesto')->constrained('puestos', 'id_puesto');
    $table->foreignId('id_mercaderia')->constrained('mercaderia', 'id_mercaderia');
    $table->foreignId('id_usuario')->constrained('users', 'id'); // Usuario que lo registró

    // Archivos Binarios (BLOB)
    $table->binary('foto')->nullable(); 
    $table->binary('firma')->nullable(); 
    
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('afiliados');
    }
};
