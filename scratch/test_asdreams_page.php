<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$request = Request::create('/asdreams', 'GET');
$response = $app->handle($request);
echo 'Status: '.$response->getStatusCode()."\n";
echo 'Length: '.strlen($response->getContent())."\n";

$html = $response->getContent();
$sections = [
    'Header' => str_contains($html, '<header class="site-header"'),
    'Hero section' => str_contains($html, 'class="asd-hero"'),
    'Brands section' => str_contains($html, 'id="asd-brands"'),
    'About section' => str_contains($html, 'id="asd-about"'),
    'Gallery section' => str_contains($html, 'id="asd-gallery"'),
    'Studio rental' => str_contains($html, 'id="studio_rental"'),
    'Clients section' => str_contains($html, 'id="clients"'),
    'Footer' => str_contains($html, 'class="footer"'),
    'Brands Popup' => str_contains($html, 'id="brandsPopup"'),
    'brands-pop-first script' => str_contains($html, 'brands-pop-first'),
];

foreach ($sections as $k => $v) {
    echo "$k: ".($v ? 'YES' : 'NO')."\n";
}
