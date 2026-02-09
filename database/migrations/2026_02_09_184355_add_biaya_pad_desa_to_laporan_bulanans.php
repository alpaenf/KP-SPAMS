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
        Schema::table('laporan_bulanans', function (Blueprint $table) {
            $table->decimal('biaya_pad_desa', 10, 2)->default(0)->after('biaya_operasional_penarik')->comment('Biaya PAD Desa bulan ini');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_bulanans', function (Blueprint $table) {
            $table->dropColumn('biaya_pad_desa');
        });
    }
};
