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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->constrained('pelanggan')->onDelete('cascade');
            $table->string('bulan_bayar', 7); // Format: YYYY-MM
            $table->date('tanggal_bayar');
            $table->decimal('jumlah_bayar', 10, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            // Index untuk performa
            $table->index(['pelanggan_id', 'bulan_bayar']);
            $table->unique(['pelanggan_id', 'bulan_bayar']); // Satu pelanggan hanya bisa bayar 1x per bulan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
