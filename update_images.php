<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$images = [
    'Sourdough' => 'https://images.unsplash.com/photo-1589367920969-ab8e050eb0e9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
    'Croissant' => 'https://images.unsplash.com/photo-1549931319-a545dcf3bc73?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
    'Pizza' => 'https://images.unsplash.com/photo-1513104890d38-7c0f4fffc0f9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
    'Rendang' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
    'Donut' => 'https://images.unsplash.com/photo-1551024601-bec78aea704b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
    'Banana' => 'https://images.unsplash.com/photo-1569864358642-9d1684040f43?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
    'Garlic' => 'https://images.unsplash.com/photo-1573140247632-f8fd74997d5c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
    'Cookies' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
    'Baguette' => 'https://images.unsplash.com/photo-1534620808146-d33bb39128b2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
    'Cokelat' => 'https://images.unsplash.com/photo-1548843269-1c4b4a11b6f0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
    'Tawar' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
    'Gandum' => 'https://images.unsplash.com/photo-1549931319-a545dcf3bc73?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
    'Keju' => 'https://images.unsplash.com/photo-1555507036-ab1f40ce88cb?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
    'Cake' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
];

$defaultImage = 'https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';

$products = \App\Models\Product::all();
$updated = 0;

foreach ($products as $product) {
    $matched = false;
    foreach ($images as $key => $url) {
        if (stripos($product->name, $key) !== false || stripos($product->description, $key) !== false) {
            $product->image_url = $url;
            $product->save();
            $matched = true;
            echo "Updated {$product->name} with {$key} image.\n";
            break;
        }
    }
    
    if (!$matched) {
        $product->image_url = $defaultImage;
        $product->save();
        echo "Updated {$product->name} with DEFAULT image.\n";
    }
    $updated++;
}

echo "\nDone updating $updated products.\n";
