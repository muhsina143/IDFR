<?php

use App\Models\CreativeProfessional;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$pros = CreativeProfessional::all();
foreach ($pros as $p) {
    echo "ID: {$p->id} | Name: {$p->name} | Cat: {$p->category} | Photo: {$p->profile_photo}\n";
    if (! empty($p->portfolio_images)) {
        echo '   Portfolio ('.count($p->portfolio_images).'): '.json_encode($p->portfolio_images)."\n";
    }
}
