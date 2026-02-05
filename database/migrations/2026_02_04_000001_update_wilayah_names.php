<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update nama wilayah di tabel pelanggan
        DB::table('pelanggan')->where('wilayah', 'Wilayah 2')->update(['wilayah' => 'Kubangsari Kulon']);
        DB::table('pelanggan')->where('wilayah', 'Wilayah 3')->update(['wilayah' => 'Kubangsari Wetan']);
        DB::table('pelanggan')->where('wilayah', 'Wilayah 4')->update(['wilayah' => 'Sokarame']);
        DB::table('pelanggan')->where('wilayah', 'Wilayah 5')->update(['wilayah' => 'Tiparjaya']);

        // Update nama wilayah di tabel laporan_bulanans
        DB::table('laporan_bulanans')->where('wilayah', 'Wilayah 2')->update(['wilayah' => 'Kubangsari Kulon']);
        DB::table('laporan_bulanans')->where('wilayah', 'Wilayah 3')->update(['wilayah' => 'Kubangsari Wetan']);
        DB::table('laporan_bulanans')->where('wilayah', 'Wilayah 4')->update(['wilayah' => 'Sokarame']);
        DB::table('laporan_bulanans')->where('wilayah', 'Wilayah 5')->update(['wilayah' => 'Tiparjaya']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert nama wilayah di tabel pelanggan
        DB::table('pelanggan')->where('wilayah', 'Kubangsari Kulon')->update(['wilayah' => 'Wilayah 2']);
        DB::table('pelanggan')->where('wilayah', 'Kubangsari Wetan')->update(['wilayah' => 'Wilayah 3']);
        DB::table('pelanggan')->where('wilayah', 'Sokarame')->update(['wilayah' => 'Wilayah 4']);
        DB::table('pelanggan')->where('wilayah', 'Tiparjaya')->update(['wilayah' => 'Wilayah 5']);

        // Revert nama wilayah di tabel laporan_bulanans
        DB::table('laporan_bulanans')->where('wilayah', 'Kubangsari Kulon')->update(['wilayah' => 'Wilayah 2']);
        DB::table('laporan_bulanans')->where('wilayah', 'Kubangsari Wetan')->update(['wilayah' => 'Wilayah 3']);
        DB::table('laporan_bulanans')->where('wilayah', 'Sokarame')->update(['wilayah' => 'Wilayah 4']);
        DB::table('laporan_bulanans')->where('wilayah', 'Tiparjaya')->update(['wilayah' => 'Wilayah 5']);
    }
};
