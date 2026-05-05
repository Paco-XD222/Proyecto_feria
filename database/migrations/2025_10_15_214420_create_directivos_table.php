<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones (CREA la tabla).
     */
    public function up(): void
    {
        // Creamos la tabla 'directivos' con las columnas de tu modelo.
        Schema::create('directivos', function (Blueprint $table) {
            
            // 1. Llave Primaria (Coincide con $primaryKey = 'id_directivo' en el modelo)
            $table->id('id_directivo'); 

            // 2. Llaves Foráneas y Atributos
            // Nota: Asumo que 'id_gestion' y 'id_afiliado' son enteros positivos.
            $table->unsignedBigInteger('id_gestion');
            $table->unsignedBigInteger('id_afiliado'); // Llave foránea que apunta a Afiliado
            
            // 3. Columnas de la tabla (Basado en tu $fillable)
            // Se asumen tipos de datos comunes (string para texto, date para fechas).
            $table->string('cargo_directivo', 100);
            $table->date('fecha_posesion');
            $table->date('fecha_conclusion')->nullable();
            
            $table->string('nombre_directivo', 100);
            $table->string('apellido_paterno_directivo', 100)->nullable();
            $table->string('apellido_materno_directivo', 100)->nullable();
            
            $table->text('observaciones')->nullable();

            // 4. Timestamps de Laravel
            $table->timestamps();

            // 5. Definición de Llaves Foráneas (Asumiendo que las tablas existen)
            // Debe asegurarse que 'feria_gestion' y 'afiliados' ya se crearon en migraciones anteriores.
            $table->foreign('id_gestion')->references('id_gestion')->on('feria_gestion')->onDelete('cascade');
            $table->foreign('id_afiliado')->references('id_afiliado')->on('afiliados')->onDelete('cascade');
        });
    }

    /**
     * Revierte las migraciones (ELIMINA la tabla).
     */
    public function down(): void
    {
        // Se elimina la tabla si es necesario hacer un rollback.
        Schema::dropIfExists('directivos');
    }
};