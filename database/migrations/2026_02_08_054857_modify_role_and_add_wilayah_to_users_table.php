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
        Schema::table('users', function (Blueprint $table) {
            // Step 1: Update semua user dengan role 'pengelola' jadi 'admin' dulu
            DB::table('users')->where('role', 'pengelola')->update(['role' => 'admin']);
            
            // Step 2: Modify kolom role dari enum('admin','pengelola') ke enum('admin','penarik')
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'penarik') NOT NULL DEFAULT 'admin'");
            
            // Step 3: Tambahkan kolom wilayah baru
            $table->enum('wilayah', ['dawuhan', 'kubangsari_kulon', 'kubangsari_wetan', 'sokarame', 'tiparjaya'])
                  ->nullable()
                  ->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kembalikan role ke enum lama
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'pengelola') NOT NULL DEFAULT 'pengelola'");
            
            // Hapus kolom wilayah
            $table->dropColumn('wilayah');
        });
    }
};
