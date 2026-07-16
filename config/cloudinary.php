<?php

$cloudinaryUrl = env('CLOUDINARY_URL');
$parsedUrl = $cloudinaryUrl ? parse_url($cloudinaryUrl) : null;

return [
    'cloud_name' => env('CLOUDINARY_CLOUD_NAME', $parsedUrl['host'] ?? ''),
    'api_key'    => env('CLOUDINARY_API_KEY', $parsedUrl['user'] ?? ''),
    'api_secret' => env('CLOUDINARY_API_SECRET', $parsedUrl['pass'] ?? ''),
    'url'        => $cloudinaryUrl,
];
