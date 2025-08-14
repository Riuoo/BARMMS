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
            $table->integer('dose_number')->default(1);
            $table->date('next_dose_date')->nullable();
            // Admin who administered the vaccine (FK to barangay_profiles)
            $table->unsignedBigInteger('administered_by')->nullable();
            
            $table->timestamps();
            
            $table->foreign('resident_id')->references('id')->on('residents')->onDelete('cascade');
            $table->foreign('child_profile_id')->references('id')->on('child_profiles')->onDelete('cascade');
            $table->foreign('administered_by')->references('id')->on('barangay_profiles')->onDelete('set null');
            
            // Add index to improve query performance
            $table->index(['resident_id', 'child_profile_id']);
            $table->index('administered_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaccination_records');
    }
}; 