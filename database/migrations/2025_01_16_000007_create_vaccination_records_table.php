<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vaccination_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resident_id')->nullable(); // Can be null for children without accounts
            $table->unsignedBigInteger('child_profile_id')->nullable(); // For children without accounts
            $table->string('vaccine_name');
            $table->enum('vaccine_type', ['COVID-19', 'Influenza', 'Pneumonia', 'Tetanus', 'Hepatitis B', 'MMR', 'Varicella', 'HPV', 'DTaP', 'Pneumococcal', 'Rotavirus', 'Hib', 'Other']);
            $table->date('vaccination_date');
            $table->string('batch_number')->nullable();
            $table->string('manufacturer')->nullable();
            $table->integer('dose_number')->default(1);
            $table->integer('total_doses_required')->default(1);
            $table->date('next_dose_date')->nullable();
            $table->string('administered_by')->nullable();
            $table->text('side_effects')->nullable();
            $table->text('notes')->nullable();
            $table->enum('age_group', ['Infant', 'Toddler', 'Child', 'Adolescent', 'Adult', 'Elderly'])->nullable();
            $table->integer('age_at_vaccination')->nullable(); // Age in months for infants, years for others
            $table->boolean('is_booster')->default(false);
            $table->boolean('is_annual')->default(false);
            $table->timestamps();
            
            $table->foreign('resident_id')->references('id')->on('residents')->onDelete('cascade');
            $table->foreign('child_profile_id')->references('id')->on('child_profiles')->onDelete('cascade');
            
            // Add index to improve query performance
            $table->index(['resident_id', 'child_profile_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaccination_records');
    }
}; 