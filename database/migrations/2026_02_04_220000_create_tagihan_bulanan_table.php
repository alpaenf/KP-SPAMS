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
        Schema::create('tagihan_bulanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->constrained('pelanggan')->onDelete('cascade');
            $table->string('bulan', 7); // Format: YYYY-MM
            $table->decimal('meteran_sebelum', 10, 2)->nullable()->comment('Meteran bulan lalu (m³)');
            $table->decimal('meteran_sesudah', 10, 2)->nullable()->comment('Meteran bulan ini (m³)');
            $table->decimal('pemakaian_kubik', 10, 2)->nullable()->comment('Pemakaian dalam m³');
            $table->decimal('tarif_per_kubik', 10, 2)->default(2000)->comment('Tarif per m³ (default Rp 2.000)');
            $table->boolean('ada_abunemen')->default(true)->comment('Apakah dikenakan biaya abunemen');
            $table->decimal('biaya_abunemen', 10, 2)->default(3000)->comment('Biaya abunemen (default Rp 3.000)');
            $table->decimal('total_tagihan', 10, 2)->default(0)->comment('Total tagihan bulan ini');
            $table->enum('status_bayar', ['BELUM_BAYAR', 'SUDAH_BAYAR', 'CICILAN', 'TUNGGAKAN'])->default('BELUM_BAYAR');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            // Index untuk performa
            $table->index(['pelanggan_id', 'bulan']);
            $table->unique(['pelanggan_id', 'bulan']); // Satu pelanggan hanya punya 1 tagihan per bulan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_bulanan');
    }
};
