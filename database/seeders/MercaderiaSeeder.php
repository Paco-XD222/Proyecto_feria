<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MercaderiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        DB::table('mercaderia')->insert([
            ['clase_mercaderia' => 'Frutas y Verduras'],
            ['clase_mercaderia' => 'Cereales y Granos'],
            ['clase_mercaderia' => 'Lácteos y Derivados'],
            ['clase_mercaderia' => 'Chamarras'],
            ['clase_mercaderia' => 'Comida Preparada'],
        ]);
    }
}
