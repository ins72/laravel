<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Site;
use App\Models\Product;
use App\Models\User;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        // Get users
        $admin = User::where('email', 'admin@example.com')->first();
        $user = User::where('email', 'user@example.com')->first();

        // Create sample sites
        $site1 = Site::create([
            'user_id' => $admin->id,
            'name' => 'Admin Site',
            'address' => 'admin-site',
            '_slug' => 'admin-site',
            'status' => 1,
            'published' => 1,
        ]);

        $site2 = Site::create([
            'user_id' => $user->id,
            'name' => 'User Site',
            'address' => 'user-site',
            '_slug' => 'user-site',
            'status' => 1,
            'published' => 1,
        ]);

        // Create sample products
        Product::create([
            'user_id' => $admin->id,
            'site_id' => $site1->id,
            'name' => 'Premium Product',
            'slug' => 'premium-product',
            'price' => 99.99,
            'price_type' => 1,
            'description' => 'A high-quality premium product',
            'status' => 1,
        ]);

        Product::create([
            'user_id' => $user->id,
            'site_id' => $site2->id,
            'name' => 'Basic Product',
            'slug' => 'basic-product',
            'price' => 29.99,
            'price_type' => 1,
            'description' => 'A basic product for testing',
            'status' => 1,
        ]);
    }
}
