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
            if (!Schema::hasColumn('tagihan_bulanan', 'foto_meteran')) {
                $table->string('foto_meteran')->nullable()->after('status_bayar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_bulanan', function (Blueprint $table) {
            if (Schema::hasColumn('tagihan_bulanan', 'foto_meteran')) {
                $table->dropColumn('foto_meteran');
            }
        });
    }
};
