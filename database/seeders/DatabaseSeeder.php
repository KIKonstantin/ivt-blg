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
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
            'name' => "Iveta",
            "email" => "admin@iveta.bg",
            "password" => '123123', 
            'is_admin' => true,
        ]);

        $this->call([
            CategorySeeder::class,
            GallerySeeder::class,
            ArticleSeeder::class,
        ]);
        
    }
}
