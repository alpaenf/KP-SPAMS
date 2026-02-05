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
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->decimal('meteran_sebelum', 10, 2)->nullable()->after('pelanggan_id');
            $table->decimal('meteran_sesudah', 10, 2)->nullable()->after('meteran_sebelum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn(['meteran_sebelum', 'meteran_sesudah']);
        });
    }
};
