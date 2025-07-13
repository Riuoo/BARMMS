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
        Schema::create('patient_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resident_id');
            $table->string('patient_number')->unique();
            $table->string('blood_type')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medical_history')->nullable();
            $table->text('family_medical_history')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_number')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->enum('blood_pressure_status', ['Normal', 'Pre-hypertension', 'Hypertension'])->nullable();
            $table->decimal('height_cm', 5, 2)->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->decimal('bmi', 4, 2)->nullable();
            $table->text('current_medications')->nullable();
            $table->text('lifestyle_factors')->nullable(); // smoking, alcohol, exercise
            $table->enum('risk_level', ['Low', 'Medium', 'High', 'Critical'])->default('Low');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('resident_id')->references('id')->on('residents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_records');
    }
}; 