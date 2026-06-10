<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'password' => bcrypt('12345678'),
            'title_job' => 'Administrador',
            'user_type' => '1',
            'active' => true,
        ]);

        User::factory()->create([
            'name' => 'Almacenista',
            'email' => 'almacenista@almacenista.com',
            'password' => bcrypt('12345678'),
            'title_job' => 'Almacenista',
            'user_type' => '2',
            'active' => true,
        ]);

        User::factory()->create([
            'name' => 'Líder de producción',
            'email' => 'lider@produccion.com',
            'password' => bcrypt('12345678'),
            'title_job' => 'Líder de producción',
            'user_type' => '3',
            'active' => true,
        ]);

        User::factory()->create([
            'name' => 'Personal de secado',
            'email' => 'personal@secado.com',
            'password' => bcrypt('12345678'),
            'title_job' => 'Personal de secado',
            'user_type' => '4',
            'active' => true,
        ]);

        User::factory()->create([
            'name' => 'Calidad',
            'email' => 'calidad@calidad.com',
            'password' => bcrypt('12345678'),
            'title_job' => 'Calidad',
            'user_type' => '5',
            'active' => true,
        ]);

    }
}
