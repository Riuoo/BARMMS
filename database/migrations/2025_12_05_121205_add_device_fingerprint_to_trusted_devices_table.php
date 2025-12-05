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
        // This migration is no longer needed as device_fingerprint is now in the original migration
        // But we'll keep it safe in case it's already been run
        Schema::table('trusted_devices', function (Blueprint $table) {
            if (!Schema::hasColumn('trusted_devices', 'device_fingerprint')) {
                // Check if device_identifier exists first
                if (Schema::hasColumn('trusted_devices', 'device_identifier')) {
                    $table->string('device_fingerprint', 64)->nullable()->after('device_identifier')->index();
                } else {
                    // If device_identifier doesn't exist, add it first, then add fingerprint
                    $table->string('device_identifier', 64)->index();
                    $table->string('device_fingerprint', 64)->nullable()->after('device_identifier')->index();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trusted_devices', function (Blueprint $table) {
            if (Schema::hasColumn('trusted_devices', 'device_fingerprint')) {
                $table->dropColumn('device_fingerprint');
            }
        });
    }
};
