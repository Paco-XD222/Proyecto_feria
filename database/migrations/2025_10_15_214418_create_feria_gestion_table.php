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
Schema::create('feria_gestion', function (Blueprint $table) {
    $table->id('id_gestion');
    $table->string('nombre_gestion', 100);
    $table->date('fecha_inicio');
    $table->date('fecha_fin');
    // Foreign Key: Usuario que registró esta gestión
    $table->foreignId('id_usuario')->constrained('users'); 
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feria_gestion');
    }
};
