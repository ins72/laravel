<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;
use App\Models\ProductCouponCode;
use App\Models\ProductShippingLocation;
use Illuminate\Support\Facades\Hash;

class EcommerceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test user if it doesn't exist
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create sample products
        $products = [
            [
                'name' => 'Wireless Bluetooth Headphones',
                'description' => 'High-quality wireless headphones with noise cancellation and long battery life. Perfect for music lovers and professionals.',
                'price' => 99.99,
                'price_type' => 1, // Fixed price
                'productType' => 'electronics',
                'stock' => 50,
                'sku' => 'WH-001',
                'status' => 1,
                'featured_img' => [
                    'url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop',
                    'alt' => 'Wireless Headphones'
                ],
                'media' => [
                    [
                        'url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop',
                        'alt' => 'Wireless Headphones Front'
                    ],
                    [
                        'url' => 'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=400&h=400&fit=crop',
                        'alt' => 'Wireless Headphones Side'
                    ]
                ],
                'extra' => [
                    'brand' => 'AudioTech',
                    'color' => 'Black',
                    'connectivity' => 'Bluetooth 5.0',
                    'battery_life' => '20 hours'
                ]
            ],
            [
                'name' => 'Smart Fitness Watch',
                'description' => 'Advanced fitness tracking watch with heart rate monitor, GPS, and smartphone connectivity. Track your workouts and health metrics.',
                'price' => 199.99,
                'price_type' => 1,
                'productType' => 'electronics',
                'stock' => 25,
                'sku' => 'SFW-002',
                'status' => 1,
                'featured_img' => [
                    'url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop',
                    'alt' => 'Smart Fitness Watch'
                ],
                'media' => [
                    [
                        'url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop',
                        'alt' => 'Smart Watch Front'
                    ]
                ],
                'extra' => [
                    'brand' => 'FitTech',
                    'color' => 'Silver',
                    'water_resistant' => 'Yes (5ATM)',
                    'battery_life' => '7 days'
                ]
            ],
            [
                'name' => 'Organic Cotton T-Shirt',
                'description' => 'Comfortable and eco-friendly cotton t-shirt made from 100% organic materials. Available in multiple colors and sizes.',
                'price' => 29.99,
                'price_type' => 1,
                'productType' => 'clothing',
                'stock' => 100,
                'sku' => 'OCT-003',
                'status' => 1,
                'featured_img' => [
                    'url' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&h=400&fit=crop',
                    'alt' => 'Organic Cotton T-Shirt'
                ],
                'extra' => [
                    'brand' => 'EcoWear',
                    'material' => '100% Organic Cotton',
                    'care_instructions' => 'Machine wash cold, tumble dry low',
                    'sustainable' => 'Yes'
                ]
            ],
            [
                'name' => 'Programming Fundamentals Book',
                'description' => 'Comprehensive guide to programming fundamentals covering variables, loops, functions, and object-oriented programming concepts.',
                'price' => 39.99,
                'price_type' => 1,
                'productType' => 'books',
                'stock' => 75,
                'sku' => 'PFB-004',
                'status' => 1,
                'featured_img' => [
                    'url' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400&h=400&fit=crop',
                    'alt' => 'Programming Book'
                ],
                'extra' => [
                    'author' => 'John Smith',
                    'pages' => '450',
                    'language' => 'English',
                    'format' => 'Paperback'
                ]
            ],
            [
                'name' => 'Modern Coffee Table',
                'description' => 'Elegant modern coffee table with clean lines and durable construction. Perfect for living rooms and home offices.',
                'price' => 299.99,
                'price_type' => 1,
                'productType' => 'home',
                'stock' => 15,
                'sku' => 'MCT-005',
                'status' => 1,
                'featured_img' => [
                    'url' => 'https://images.unsplash.com/photo-1533090481720-856c6e3c1fdc?w=400&h=400&fit=crop',
                    'alt' => 'Modern Coffee Table'
                ],
                'extra' => [
                    'brand' => 'HomeStyle',
                    'material' => 'Solid Wood',
                    'dimensions' => '120cm x 60cm x 45cm',
                    'assembly_required' => 'Yes'
                ]
            ],
            [
                'name' => 'Premium Coffee Maker',
                'description' => 'Professional-grade coffee maker with programmable settings, thermal carafe, and built-in grinder for the perfect cup every time.',
                'price' => 149.99,
                'price_type' => 1,
                'productType' => 'home',
                'stock' => 30,
                'sku' => 'PCM-006',
                'status' => 1,
                'featured_img' => [
                    'url' => 'https://images.unsplash.com/photo-1517668808822-9ebb02f2a0e6?w=400&h=400&fit=crop',
                    'alt' => 'Premium Coffee Maker'
                ],
                'media' => [
                    [
                        'url' => 'https://images.unsplash.com/photo-1517668808822-9ebb02f2a0e6?w=400&h=400&fit=crop',
                        'alt' => 'Coffee Maker Front'
                    ],
                    [
                        'url' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400&h=400&fit=crop',
                        'alt' => 'Coffee Maker Side'
                    ]
                ],
                'extra' => [
                    'brand' => 'BrewMaster',
                    'capacity' => '12 cups',
                    'warranty' => '2 years',
                    'energy_efficient' => 'Yes'
                ]
            ]
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['sku' => $productData['sku']],
                array_merge($productData, [
                    'user_id' => $user->id,
                    'slug' => \Str::slug($productData['name']),
                    'status' => 1,
                    'enableOptions' => false,
                    'isDeal' => false,
                    'enableBid' => false,
                    'position' => 0
                ])
            );
        }

        // Create sample coupon codes
        $coupons = [
            [
                'code' => 'WELCOME10',
                'discount_type' => 'percentage',
                'discount_amount' => 10,
                'min_order_amount' => 50.00,
                'max_uses' => 100,
                'max_uses_per_user' => 1,
                'expires_at' => now()->addMonths(3),
                'status' => 1
            ],
            [
                'code' => 'SAVE20',
                'discount_type' => 'fixed',
                'discount_amount' => 20.00,
                'min_order_amount' => 100.00,
                'max_uses' => 50,
                'max_uses_per_user' => 1,
                'expires_at' => now()->addMonths(1),
                'status' => 1
            ]
        ];

        foreach ($coupons as $couponData) {
            ProductCouponCode::firstOrCreate(
                ['code' => $couponData['code']],
                $couponData
            );
        }

        // Create sample shipping locations
        $shippingLocations = [
            ['name' => 'United States', 'code' => 'US', 'shipping_cost' => 0.00],
            ['name' => 'Canada', 'code' => 'CA', 'shipping_cost' => 15.00],
            ['name' => 'United Kingdom', 'code' => 'GB', 'shipping_cost' => 25.00],
            ['name' => 'Australia', 'code' => 'AU', 'shipping_cost' => 30.00],
            ['name' => 'Germany', 'code' => 'DE', 'shipping_cost' => 20.00],
            ['name' => 'France', 'code' => 'FR', 'shipping_cost' => 20.00],
            ['name' => 'Japan', 'code' => 'JP', 'shipping_cost' => 35.00],
            ['name' => 'India', 'code' => 'IN', 'shipping_cost' => 25.00]
        ];

        foreach ($shippingLocations as $locationData) {
            ProductShippingLocation::firstOrCreate(
                ['code' => $locationData['code']],
                $locationData
            );
        }

        $this->command->info('Ecommerce sample data seeded successfully!');
        $this->command->info('Test user: test@example.com / password');
        $this->command->info('Sample products, coupons, and shipping locations created.');
    }
}