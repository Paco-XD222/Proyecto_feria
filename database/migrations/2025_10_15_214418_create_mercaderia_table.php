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
Schema::create('mercaderia', function (Blueprint $table) {
    $table->id('id_mercaderia'); // Usamos el nombre que definiste
    $table->string('clase_mercaderia', 100)->unique();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercaderia');
    }
};
