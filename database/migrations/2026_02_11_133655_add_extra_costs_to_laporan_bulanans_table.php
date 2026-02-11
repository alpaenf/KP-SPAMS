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
            $table->decimal('biaya_operasional_lapangan', 15, 2)->default(0)->nullable();
            $table->decimal('biaya_lain_lain', 15, 2)->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_bulanans', function (Blueprint $table) {
            $table->dropColumn(['biaya_operasional_lapangan', 'biaya_lain_lain']);
        });
    }
};
