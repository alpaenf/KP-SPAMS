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
        Schema::create('laporan_bulanans', function (Blueprint $table) {
            $table->id();
            $table->string('bulan', 7)->unique()->comment('Format: YYYY-MM');
            $table->decimal('biaya_operasional_penarik', 10, 2)->default(0)->comment('Biaya operasional penarik bulan ini');
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            $table->index('bulan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_bulanans');
    }
};
