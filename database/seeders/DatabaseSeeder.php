<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PenggunaSeeder::class, // <-- Ganti UserSeeder jadi ini
            SatuanSeeder::class,
            ProdukSeeder::class,
            PenjualanSeeder::class,
            UserSeeder::class,
        ]);
    }
}
