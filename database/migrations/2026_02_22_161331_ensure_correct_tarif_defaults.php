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
        // 1. Update existing records that have 2500 (old default)
        DB::table('tagihan_bulanan')
            ->where('tarif_per_kubik', 2500)
            ->update(['tarif_per_kubik' => 2000]);

        // 2. Change column default to 2000 and 3000
        Schema::table('tagihan_bulanan', function (Blueprint $table) {
            $table->decimal('tarif_per_kubik', 10, 2)->default(2000)->change();
            $table->decimal('biaya_abunemen', 10, 2)->default(3000)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_bulanan', function (Blueprint $table) {
            $table->decimal('tarif_per_kubik', 10, 2)->default(2500)->change();
            $table->decimal('biaya_abunemen', 10, 2)->default(0)->change();
        });
    }
};
