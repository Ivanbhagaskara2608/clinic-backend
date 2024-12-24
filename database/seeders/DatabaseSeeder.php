<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Specialist;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Specialist::create([
            'name' => 'Dokter Umum',
        ]);

        Specialist::create([
            'name' => 'Dokter Gigi',
        ]);

        Specialist::create([
            'name' => 'Dokter Kandungan',
        ]);
    }
}
