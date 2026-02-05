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
        // Update existing data first
        DB::table('pelanggan')->where('wilayah', 'Wilayah 1')->update(['wilayah' => 'Dawuhan']);
        DB::table('laporan_bulanans')->where('wilayah', 'Wilayah 1')->update(['wilayah' => 'Dawuhan']);

        // Change default value
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->string('wilayah', 100)->default('Dawuhan')->change();
        });

        Schema::table('laporan_bulanans', function (Blueprint $table) {
            $table->string('wilayah', 100)->default('Dawuhan')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert default value
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->string('wilayah', 100)->default('Wilayah 1')->change();
        });

        Schema::table('laporan_bulanans', function (Blueprint $table) {
            $table->string('wilayah', 100)->default('Wilayah 1')->change();
        });

        // Revert data - ONLY revert 'Dawuhan' that might have been 'Wilayah 1'.
        // This is lossy if there were other 'Dawuhan' inputs, but assuming strictly migration rollback of this step:
        DB::table('pelanggan')->where('wilayah', 'Dawuhan')->update(['wilayah' => 'Wilayah 1']);
        DB::table('laporan_bulanans')->where('wilayah', 'Dawuhan')->update(['wilayah' => 'Wilayah 1']);
    }
};
