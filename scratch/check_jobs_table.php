<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

foreach (['jobs', 'failed_jobs'] as $table) {
    if (Schema::hasTable($table)) {
        echo "Table '$table' exists.\n";
    } else {
        echo "Table '$table' does NOT exist.\n";
    }
}
