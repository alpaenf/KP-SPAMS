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
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('qris_image')->nullable(); // Path ke gambar QRIS
            $table->string('bank_name')->default('BRI');
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->text('payment_instructions')->nullable();
            $table->boolean('qris_enabled')->default(false);
            $table->boolean('bank_transfer_enabled')->default(true);
            $table->timestamps();
        });

        // Insert default data
        DB::table('payment_settings')->insert([
            'bank_name' => 'BRI',
            'account_number' => '1234-5678-9012',
            'account_name' => 'KP-SPAMS Desa',
            'payment_instructions' => 'Transfer ke rekening di atas atau scan QRIS',
            'qris_enabled' => false,
            'bank_transfer_enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
