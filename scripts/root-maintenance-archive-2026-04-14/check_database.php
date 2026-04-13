<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CEK DATABASE ===\n\n";

try {
    $dbName = DB::getDatabaseName();
    echo "Database: $dbName\n\n";
    
    // List all tables
    $tables = DB::select('SHOW TABLES');
    echo "Tables yang tersedia:\n";
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "  - $tableName\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
