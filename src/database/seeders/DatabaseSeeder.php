<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create your Admin Account
        User::create([
            'name' => 'Lahcen Admin',
            'email' => 'lahcen.maskour2003@gmail.com',
            'password' => Hash::make('lahcen123'),
            'role' => UserRole::ADMIN,
        ]);

        // 2. Create your Customer Account
        User::create([
            'name' => 'Lahcen Customer',
            'email' => 'lahcen.maskour@gmail.com',
            'password' => Hash::make('lahcen123'),
            'role' => UserRole::CUSTOMER,
        ]);

        // 3. Create Categories and Products
        $categories = ['Zero Waste', 'Organic Food', 'Eco-Fashion', 'Solar Energy'];

        foreach ($categories as $catName) {
            $category = Category::create([
                'name' => $catName,
                'slug' => str($catName)->slug(),
                'description' => "Eco-friendly products for $catName",
            ]);

            // Create 5 random products for each category
            for ($i = 1; $i <= 5; $i++) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => "Eco " . $catName . " Product " . $i,
                    'description' => "This is a high-quality sustainable item from our $catName line.",
                    'price' => rand(10, 100) + 0.99,
                    'stock_quantity' => rand(5, 50),
                    'image_url' => 'https://via.placeholder.com/400x400.png?text=Eco+Product',
                ]);
            }
        }
    }
}
