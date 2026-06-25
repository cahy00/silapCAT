<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach(\App\Models\Event::all() as $e) {
    echo $e->name . ' | Instansi: ' . $e->eventLocations->flatMap(function($l) { return $l->eventLocationInstitutions->map(function($i) { return $i->institution->name ?? ''; }); })->unique()->implode(', ') . ' | Tahun: ' . $e->formation_year . PHP_EOL;
}
