<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('child_profiles', function (Blueprint $table) {
            // Ensure purok exists and is required
            if (!Schema::hasColumn('child_profiles', 'purok')) {
                $table->string('purok')->after('address');
            } else {
                $table->string('purok')->nullable(false)->change();
            }

            // Drop unused columns to keep schema minimal
            $drop = [
                'address',
                'middle_name',
                'birth_place',
                'birth_certificate_number',
                'father_name',
                'guardian_name',
                'guardian_relationship',
                'barangay',
                'city',
                'province',
                'zip_code',
                'medical_conditions',
                'allergies',
                'special_notes',
            ];

            foreach ($drop as $col) {
                if (Schema::hasColumn('child_profiles', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('child_profiles', function (Blueprint $table) {
            // Re-create columns as nullable if rolled back (structure only)
            $recreate = [
                'middle_name' => 'string',
                'birth_place' => 'string',
                'birth_certificate_number' => 'string',
                'father_name' => 'string',
                'guardian_name' => 'string',
                'guardian_relationship' => 'string',
                'barangay' => 'string',
                'city' => 'string',
                'province' => 'string',
                'zip_code' => 'string',
            ];
            foreach ($recreate as $name => $type) {
                if (!Schema::hasColumn('child_profiles', $name)) {
                    $table->$type($name)->nullable();
                }
            }
            if (!Schema::hasColumn('child_profiles', 'medical_conditions')) {
                $table->text('medical_conditions')->nullable();
            }
            if (!Schema::hasColumn('child_profiles', 'allergies')) {
                $table->text('allergies')->nullable();
            }
            if (!Schema::hasColumn('child_profiles', 'special_notes')) {
                $table->text('special_notes')->nullable();
            }
        });
    }
};


