<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin User
        User::factory()->create([
            'name' => 'Admin Artisan',
            'email' => 'admin@artisan.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Customer User
        User::factory()->create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        // Categories
        $cat1 = \App\Models\Category::create([
            'name' => 'Ceramics',
            'slug' => 'ceramics',
            'description' => 'Handcrafted ceramic items',
            'is_active' => true,
        ]);

        $cat2 = \App\Models\Category::create([
            'name' => 'Textiles',
            'slug' => 'textiles',
            'description' => 'Woven and knitted fabrics',
            'is_active' => true,
        ]);

        // Products
        \App\Models\Product::create([
            'category_id' => $cat1->id,
            'name' => 'Hand-painted Vase',
            'slug' => 'hand-painted-vase',
            'description' => 'Beautiful hand-painted ceramic vase.',
            'price' => 250000,
            'sku' => 'VSE-001',
            'stock' => 15,
            'is_published' => true,
        ]);

        \App\Models\Product::create([
            'category_id' => $cat2->id,
            'name' => 'Woven Wall Hanging',
            'slug' => 'woven-wall-hanging',
            'description' => 'Artisanal woven textile for your wall.',
            'price' => 175000,
            'sku' => 'TXT-001',
            'stock' => 5,
            'is_published' => true,
        ]);

        $this->call(ProductSeeder::class);
    }
}
