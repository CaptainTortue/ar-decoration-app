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
        // check if users already exist to prevent duplicate seeding
        if (User::all()->count() > 0) {
            $this->command->info('Users already exist, skipping seeding.');
        } else {
            // Administrateur
            User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'is_admin' => true,
            ]);

            // Utilisateur de test
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'is_admin' => false,
            ]);
        }

        // Catégories puis objets
        $this->call([
            CategorySeeder::class,
            FurnitureObjectSeeder::class,
        ]);
    }
}
