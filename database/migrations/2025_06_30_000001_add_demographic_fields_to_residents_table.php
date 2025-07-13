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
        Schema::table('residents', function (Blueprint $table) {
            // Demographic fields for analytics
            $table->integer('age')->nullable()->after('address');
            $table->integer('family_size')->nullable()->after('age');
            $table->enum('education_level', [
                'No Education',
                'Elementary', 
                'High School',
                'Vocational',
                'College',
                'Post Graduate'
            ])->nullable()->after('family_size');
            $table->enum('income_level', [
                'Low',
                'Lower Middle',
                'Middle', 
                'Upper Middle',
                'High'
            ])->nullable()->after('education_level');
            $table->enum('employment_status', [
                'Unemployed',
                'Part-time',
                'Self-employed',
                'Full-time'
            ])->nullable()->after('income_level');
            $table->enum('health_status', [
                'Critical',
                'Poor',
                'Fair',
                'Good',
                'Excellent'
            ])->nullable()->after('employment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropColumn([
                'age',
                'family_size', 
                'education_level',
                'income_level',
                'employment_status',
                'health_status'
            ]);
        });
    }
}; 