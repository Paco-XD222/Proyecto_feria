<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FeriaGestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        DB::table('feria_gestion')->insert([
            [
                'nombre_gestion' => 'Directiva 2024-2025',
                'fecha_inicio' => Carbon::create(2024, 1, 1),
                'fecha_fin' => Carbon::create(2025, 12, 31),
                'id_usuario' => 1, // Asume que el usuario 1 es el administrador que registró esto
            ],
        ]);
    }
}
