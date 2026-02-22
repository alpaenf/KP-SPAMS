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
        // Ubah enum status_bayar jadi 4 pilihan
        DB::statement("ALTER TABLE `tagihan_bulanan` MODIFY COLUMN `status_bayar` ENUM('BELUM_BAYAR', 'SUDAH_BAYAR', 'CICILAN', 'TUNGGAKAN') NOT NULL DEFAULT 'BELUM_BAYAR'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback ke enum lama (2 pilihan)
        DB::statement("ALTER TABLE `tagihan_bulanan` MODIFY COLUMN `status_bayar` ENUM('BELUM_BAYAR', 'SUDAH_BAYAR') NOT NULL DEFAULT 'BELUM_BAYAR'");
    }
};
