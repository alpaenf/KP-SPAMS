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
            $table->string('wilayah', 100)->default('Wilayah 1')->after('kategori');
        });
        
        Schema::table('laporan_bulanans', function (Blueprint $table) {
            $table->string('wilayah', 100)->default('Wilayah 1')->after('bulan');
            
            // Update unique constraint to include wilayah
            $table->dropUnique(['bulan']);
            $table->unique(['bulan', 'wilayah']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_bulanans', function (Blueprint $table) {
            $table->dropUnique(['bulan', 'wilayah']);
            $table->unique(['bulan']);
            $table->dropColumn('wilayah');
        });
        
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropColumn('wilayah');
        });
    }
};
