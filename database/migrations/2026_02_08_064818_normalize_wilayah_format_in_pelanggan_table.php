<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Normalize wilayah format from "Title Case With Space" to "lowercase_with_underscore"
     * to match users.wilayah enum format for role-based access control
     */
    public function up(): void
    {
        // Mapping dari format lama ke format baru
        $mapping = [
            'Dawuhan' => 'dawuhan',
            'Kubangsari Kulon' => 'kubangsari_kulon',
            'Kubangsari Wetan' => 'kubangsari_wetan',
            'Sokarame' => 'sokarame',
            'Tiparjaya' => 'tiparjaya',
        ];

        foreach ($mapping as $old => $new) {
            DB::table('pelanggan')
                ->where('wilayah', $old)
                ->update(['wilayah' => $new]);
        }
    }

    /**
     * Reverse the migrations.
     * Rollback to Title Case with space format
     */
    public function down(): void
    {
        // Reverse mapping untuk rollback
        $reverseMapping = [
            'dawuhan' => 'Dawuhan',
            'kubangsari_kulon' => 'Kubangsari Kulon',
            'kubangsari_wetan' => 'Kubangsari Wetan',
            'sokarame' => 'Sokarame',
            'tiparjaya' => 'Tiparjaya',
        ];

        foreach ($reverseMapping as $new => $old) {
            DB::table('pelanggan')
                ->where('wilayah', $new)
                ->update(['wilayah' => $old]);
        }
    }
};
