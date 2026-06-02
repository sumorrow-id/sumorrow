<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$images = \App\Models\PostImage::all();
echo "Total images: " . $images->count() . "\n";
foreach($images as $img) {
    echo "Post {$img->post_id} - Pos {$img->position} - {$img->image_url}\n";
}
