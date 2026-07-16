<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Delete all existing users
User::truncate();

// Create new admin/owner user
User::create([
    'name' => 'Umar Ramadhan',
    'email' => 'umarramadhan10@gmail.com',
    'phone' => '081234567890',
    'password' => Hash::make('UmarDev2006'),
    'role' => 'admin', // Or owner, both have admin access based on routes
    'email_verified_at' => now(),
]);

echo "Old users deleted. New admin umarramadhan10@gmail.com created successfully.\n";
