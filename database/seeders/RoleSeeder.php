<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
{
    // Limpia la tabla y mantiene los IDs consistentes
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('roles')->truncate(); 

    DB::table('roles')->insert([
        // ROL 1: Administrador (Super Usuario)
        ['id' => 1, 'nombre' => 'Administrador', 'created_at' => now(), 'updated_at' => now()],
        
        // ROL 2: Personal de Registro (Directivo)
        // Este rol es quien usa el formulario de creación de Afiliados.
        ['id' => 2, 'nombre' => 'Directivo', 'created_at' => now(), 'updated_at' => now()],
        
        // ROL 3: Afiliado (Usuario Final)
        // Es crucial que este sea el ID 3, ya que tu controlador lo utiliza.
        ['id' => 3, 'nombre' => 'Afiliado', 'created_at' => now(), 'updated_at' => now()],
 ]);

    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
}
}