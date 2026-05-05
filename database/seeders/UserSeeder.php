<?php

// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // NO INSERTAMOS ROLES AQUÍ. Los roles ya son creados por el RoleSeeder.

        // Limpiamos la tabla de usuarios antes de insertar
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate(); 
        
        $now = Carbon::now();

        // 1. Crear Usuario Administrador (role_id = 1)
        DB::table('users')->insert([
            'name' => 'Admin General',
            'email' => 'admin@test.com', // Email fácil de recordar
            'password' => Hash::make('password'), 
            'role_id' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. Crear Usuario Directivo/Secretario de Prueba (role_id = 2)
        // Este usuario es el que necesita para iniciar sesión y registrar Afiliados.
        DB::table('users')->insert([
            'name' => 'Presidente',
            'email' => 'directivo@test.com',
            'password' => Hash::make('password'),
            'role_id' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}