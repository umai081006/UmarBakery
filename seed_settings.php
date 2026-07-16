<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;

$defaults = [
    'hero_bg' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
    'about_img' => 'https://images.unsplash.com/photo-1517433622965-0e62058e235e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
    'process_img' => 'https://images.unsplash.com/photo-1486427944781-dbf259a2b4a7?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
];

foreach ($defaults as $key => $value) {
    Setting::updateOrCreate(['key' => $key], ['value' => $value, 'group' => 'general']);
}

echo "Default settings seeded successfully.\n";
