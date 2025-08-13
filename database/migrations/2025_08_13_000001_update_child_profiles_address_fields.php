<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('child_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('child_profiles', 'purok')) {
                $table->string('purok')->nullable()->after('address');
            }
        });

        // Make legacy address fields nullable to allow saving only 'purok'
        try {
            DB::statement('ALTER TABLE child_profiles MODIFY barangay VARCHAR(255) NULL');
        } catch (\Throwable $e) {}
        try {
            DB::statement('ALTER TABLE child_profiles MODIFY city VARCHAR(255) NULL');
        } catch (\Throwable $e) {}
        try {
            DB::statement('ALTER TABLE child_profiles MODIFY province VARCHAR(255) NULL');
        } catch (\Throwable $e) {}
        try {
            DB::statement('ALTER TABLE child_profiles MODIFY zip_code VARCHAR(10) NULL');
        } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        Schema::table('child_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('child_profiles', 'purok')) {
                $table->dropColumn('purok');
            }
        });
        // Not reverting the nullable changes to avoid data loss
    }
};


