<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update semua tagihan yang tarif_per_kubik nya 2500 jadi 2000
        DB::table('tagihan_bulanan')
            ->where('tarif_per_kubik', 2500)
            ->update(['tarif_per_kubik' => 2000]);
        
        echo "✅ Updated all tarif from 2500 to 2000\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: kembalikan ke 2500 (kalau perlu)
        DB::table('tagihan_bulanan')
            ->where('tarif_per_kubik', 2000)
            ->update(['tarif_per_kubik' => 2500]);
    }
};
