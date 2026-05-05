<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // NO uses 'WithoutModelEvents' por ahora para simplificar.
    // use WithoutModelEvents; 

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Llama a tus Seeders personalizados en orden de dependencia.
        // Role y User (Admin) deben ir primero.
        $this->call([
            UserSeeder::class,        // Crea Roles y un Usuario Admin de prueba.
            MercaderiaSeeder::class,
            FeriaGestionSeeder::class,
            RoleSeeder::class,
        ]);
        
        // El código de fábricas (User::factory...) se elimina o comenta, ya no es necesario aquí.
    }
}
