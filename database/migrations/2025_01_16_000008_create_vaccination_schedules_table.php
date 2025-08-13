<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vaccination_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('vaccine_name');
            $table->enum('vaccine_type', ['COVID-19', 'Influenza', 'Pneumonia', 'Tetanus', 'Hepatitis B', 'MMR', 'Varicella', 'HPV', 'DTaP', 'Pneumococcal', 'Rotavirus', 'Hib', 'Other']);
            $table->enum('age_group', ['Infant', 'Toddler', 'Child', 'Adolescent', 'Adult', 'Elderly']);
            $table->integer('age_min_months')->nullable(); // Minimum age in months
            $table->integer('age_max_months')->nullable(); // Maximum age in months
            $table->integer('age_min_years')->nullable(); // Minimum age in years
            $table->integer('age_max_years')->nullable(); // Maximum age in years
            $table->integer('dose_number');
            $table->integer('total_doses_required');
            $table->integer('interval_months')->nullable(); // Interval between doses in months
            $table->integer('interval_years')->nullable(); // Interval between doses in years
            $table->boolean('is_booster')->default(false);
            $table->boolean('is_annual')->default(false);
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaccination_schedules');
    }
};
