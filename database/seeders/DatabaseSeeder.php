<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Users
        User::create([
            'name' => 'Admin Umar Bakery',
            'email' => 'admin@umarbakery.com',
            'phone' => '0811111111',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Owner Umar Bakery',
            'email' => 'owner@umarbakery.com',
            'phone' => '0822222222',
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Customer Umar Bakery',
            'email' => 'customer@umarbakery.com',
            'phone' => '0833333333',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        // 2. Create Categories
        $rotiManis = Category::create([
            'name' => 'Roti Manis',
            'slug' => 'roti-manis',
            'is_active' => true,
        ]);

        $rotiTawar = Category::create([
            'name' => 'Roti Tawar',
            'slug' => 'roti-tawar',
            'is_active' => true,
        ]);

        $pastryCake = Category::create([
            'name' => 'Pastry & Cake',
            'slug' => 'pastry-cake',
            'is_active' => true,
        ]);

        // 3. Create Products
        Product::create([
            'category_id' => $rotiManis->id,
            'name' => 'Roti Cokelat',
            'slug' => 'roti-cokelat',
            'sku' => 'UB-RTM-001',
            'description' => 'Roti manis klasik dengan isian cokelat Belgia yang melimpah dan lumer di mulut.',
            'composition' => 'Tepung terigu protein tinggi, Mentega premium, Cokelat Belgia, Gula pasir, Susu cair, Ragi instan.',
            'price' => 8000.00,
            'stock' => 50,
            'weight' => 80,
            'image_url' => null,
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $rotiManis->id,
            'name' => 'Roti Keju',
            'slug' => 'roti-keju',
            'sku' => 'UB-RTM-002',
            'description' => 'Roti manis gurih dengan filling keju cheddar dan taburan keju parut renyah di atasnya.',
            'composition' => 'Tepung terigu, Mentega, Keju Cheddar, Gula, Susu, Ragi, Telur.',
            'price' => 9000.00,
            'stock' => 40,
            'weight' => 80,
            'image_url' => null,
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $rotiTawar->id,
            'name' => 'Roti Tawar Kupas',
            'slug' => 'roti-tawar-kupas',
            'sku' => 'UB-RTT-001',
            'description' => 'Roti tawar super lembut dengan pinggiran kulit yang dikupas bersih. Sangat praktis untuk sandwich.',
            'composition' => 'Tepung terigu protein tinggi, Susu segar, Mentega putih, Gula, Ragi, Garam halus.',
            'price' => 15000.00,
            'stock' => 20,
            'weight' => 400,
            'image_url' => null,
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $rotiTawar->id,
            'name' => 'Roti Tawar Gandum',
            'slug' => 'roti-tawar-gandum',
            'sku' => 'UB-RTT-002',
            'description' => 'Roti tawar sehat tinggi serat terbuat dari tepung gandum utuh berkualitas. Rendah gula dan baik untuk diet.',
            'composition' => 'Tepung gandum utuh, Tepung gandum kasar, Air mineral, Ragi instan, Garam diet.',
            'price' => 18000.00,
            'stock' => 15,
            'weight' => 450,
            'image_url' => null,
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $pastryCake->id,
            'name' => 'Croissant Butter',
            'slug' => 'croissant-butter',
            'sku' => 'UB-PSC-001',
            'description' => 'Pastry khas Perancis bertekstur renyah di luar, berongga lembut di dalam, dengan aroma butter premium yang kuat.',
            'composition' => 'Tepung terigu French T55, Premium French Butter, Air es, Ragi, Garam, Gula.',
            'price' => 17000.00,
            'stock' => 25,
            'weight' => 70,
            'image_url' => null,
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $pastryCake->id,
            'name' => 'Cheese Cake Slice',
            'slug' => 'cheese-cake-slice',
            'sku' => 'UB-PSC-002',
            'description' => 'Cheese cake panggang bergaya New York yang padat dan creamy, disajikan per slice dengan base biskuit gurih.',
            'composition' => 'Cream cheese Anchor, Whipping cream, Gula kastor, Telur segar, Biskuit Marie, Butter cair.',
            'price' => 35000.00,
            'stock' => 10,
            'weight' => 150,
            'image_url' => null,
            'is_active' => true,
        ]);
    }
}
