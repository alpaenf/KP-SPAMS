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
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->string('bulan_bayar')->nullable()->after('google_maps_url')->comment('Bulan terakhir bayar (YYYY-MM)');
            $table->date('tanggal_bayar')->nullable()->after('bulan_bayar')->comment('Tanggal terakhir bayar');
            $table->decimal('jumlah_bayar', 10, 2)->nullable()->after('tanggal_bayar')->default(0)->comment('Jumlah pembayaran terakhir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropColumn(['bulan_bayar', 'tanggal_bayar', 'jumlah_bayar']);
        });
    }
};
