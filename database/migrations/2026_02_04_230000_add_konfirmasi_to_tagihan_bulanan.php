<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan_bulanan', function (Blueprint $table) {
            $table->string('bukti_transfer')->nullable()->after('status_bayar');
            $table->timestamp('konfirmasi_at')->nullable()->after('bukti_transfer');
            $table->enum('status_konfirmasi', ['none', 'pending', 'approved', 'rejected'])->default('none')->after('konfirmasi_at');
            $table->text('catatan_admin')->nullable()->after('status_konfirmasi');
        });
    }

    public function down(): void
    {
        Schema::table('tagihan_bulanan', function (Blueprint $table) {
            $table->dropColumn(['bukti_transfer', 'konfirmasi_at', 'status_konfirmasi', 'catatan_admin']);
        });
    }
};
