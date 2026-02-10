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
        // Administrateur
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'is_admin' => true,
                'password' => bcrypt('password'), // Replace 'password' with a secure default password
            ]
        );

        // Utilisateur de test
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'is_admin' => false,
                'password' => bcrypt('password'), // Replace 'password' with a secure default password
            ]
        );
        // Catégories puis objets
        $this->call([
            CategorySeeder::class,
            FurnitureObjectSeeder::class,
        ]);
    }
}
