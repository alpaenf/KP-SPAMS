<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tagihan_bulanan', function (Blueprint $table) {
            // Kolom untuk tracking total yang sudah dibayar (untuk cicilan)
            $table->decimal('jumlah_terbayar', 10, 2)->default(0)->after('total_tagihan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_bulanan', function (Blueprint $table) {
            $table->dropColumn('jumlah_terbayar');
        });
    }
};
