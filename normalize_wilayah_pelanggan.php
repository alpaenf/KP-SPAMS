<?php
// Normalize wilayah format in pelanggan table
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pelanggan;
use Illuminate\Support\Facades\DB;

echo "=== Normalize Wilayah Format Pelanggan ===\n\n";

// Mapping dari format lama (Title Case + spasi) ke format baru (lowercase + underscore)
$mapping = [
    'Dawuhan' => 'dawuhan',
    'Kubangsari Kulon' => 'kubangsari_kulon',
    'Kubangsari Wetan' => 'kubangsari_wetan',
    'Sokarame' => 'sokarame',
    'Tiparjaya' => 'tiparjaya',
];

DB::beginTransaction();

try {
    foreach ($mapping as $old => $new) {
        $count = Pelanggan::where('wilayah', $old)->count();
        
        if ($count > 0) {
            echo "Updating '{$old}' → '{$new}' ({$count} pelanggan)...\n";
            
            Pelanggan::where('wilayah', $old)
                ->update(['wilayah' => $new]);
            
            $verified = Pelanggan::where('wilayah', $new)->count();
            echo "   ✅ Verified: {$verified} pelanggan now have wilayah '{$new}'\n\n";
        } else {
            echo "⏭️  Skipped '{$old}' → no pelanggan found\n\n";
        }
    }
    
    DB::commit();
    
    echo "\n=== HASIL AKHIR ===\n";
    $results = Pelanggan::select('wilayah', DB::raw('count(*) as total'))
        ->groupBy('wilayah')
        ->orderBy('wilayah')
        ->get();
    
    foreach ($results as $r) {
        echo "   '{$r->wilayah}' → {$r->total} pelanggan\n";
    }
    
    echo "\n✅ Normalisasi wilayah selesai!\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
