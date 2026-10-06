<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');

        // Categories
        $categories = ['Home Decor', 'Textiles', 'Accessories', 'Pottery'];
        foreach ($categories as $cat) {
            \App\Models\Category::firstOrCreate([
                'name' => $cat
            ], [
                'slug' => \Illuminate\Support\Str::slug($cat),
                'is_active' => true
            ]);
        }

        $categoryIds = \App\Models\Category::pluck('id')->toArray();
        if (empty($categoryIds)) {
            $categoryIds = [1];
        }

        for ($i = 1; $i <= 20; $i++) {
            $name = ucfirst($faker->words(3, true));
            \App\Models\Product::create([
                'category_id' => $faker->randomElement($categoryIds),
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name) . '-' . uniqid(),
                'description' => $faker->paragraph(3),
                'material' => $faker->words(2, true),
                'price' => $faker->numberBetween(100000, 2000000),
                'compare_at_price' => null,
                'sku' => 'SKU-' . strtoupper($faker->bothify('???-###')),
                'stock' => $faker->numberBetween(10, 100),
                'weight' => $faker->numberBetween(100, 2000),
                'badge' => $faker->randomElement(['New', 'Sale', null]),
                'rating' => $faker->randomFloat(1, 3, 5),
                'is_published' => true,
                'image' => null, // user requested no images
            ]);
        }
    }
}
