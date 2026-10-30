<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\CreativeProfessional;
use App\Models\Designer;
use App\Models\ModelProfile;
use Illuminate\Contracts\Console\Kernel;

echo "--- MODEL PROFILES ---\n";
$models = ModelProfile::take(3)->get();
foreach ($models as $m) {
    echo "Model ID: {$m->id}, Name: {$m->name}, Gender: {$m->gender}, Category/Level: {$m->level}, Photo: {$m->photo_url}\n";
}

echo "\n--- CREATIVE PROFESSIONALS ---\n";
$pros = CreativeProfessional::take(5)->get();
foreach ($pros as $p) {
    echo "Pro ID: {$p->id}, Slug: {$p->slug}, Name: {$p->name}, Category: {$p->category}, Photo: {$p->photo_url}\n";
}

echo "\n--- FASHION DESIGNERS ---\n";
$designers = Designer::take(3)->get();
foreach ($designers as $d) {
    echo "Designer ID: {$d->id}, Slug: {$d->slug}, Name: {$d->name}, Brand: {$d->brand_name}, Photo: {$d->photo_url}\n";
}
