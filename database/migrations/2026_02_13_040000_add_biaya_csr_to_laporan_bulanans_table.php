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
            $table->decimal('biaya_csr', 15, 2)->default(0)->nullable()->after('biaya_lain_lain')->comment('Biaya Corporate Social Responsibility');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_bulanans', function (Blueprint $table) {
            $table->dropColumn('biaya_csr');
        });
    }
};
