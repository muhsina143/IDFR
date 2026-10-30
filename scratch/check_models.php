<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\ModelProfile;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

$m = new ModelProfile;
echo 'Table: '.$m->getTable()."\n";
$cols = DB::select('DESCRIBE '.$m->getTable());
foreach ($cols as $c) {
    echo "{$c->Field} | {$c->Type} | Null: {$c->Null} | Default: {$c->Default}\n";
}
