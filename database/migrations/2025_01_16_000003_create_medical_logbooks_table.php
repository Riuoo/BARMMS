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
        Schema::create('medical_logbooks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resident_id');
            $table->dateTime('consultation_datetime')->useCurrent()->change();
            $table->string('consultation_type');
            $table->text('chief_complaint');
            $table->text('symptoms');
            $table->text('diagnosis')->nullable();
            $table->text('treatment_plan');
            $table->text('prescribed_medications')->nullable();
            $table->text('lab_tests_ordered')->nullable();
            $table->text('lab_results')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->integer('blood_pressure_systolic')->nullable();
            $table->integer('blood_pressure_diastolic')->nullable();
            $table->integer('pulse_rate')->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->decimal('height_cm', 5, 2)->nullable();
            $table->text('physical_examination');
            $table->text('notes')->nullable();
            $table->string('attending_health_worker');
            $table->date('follow_up_date')->nullable();
            $table->enum('status', ['Completed', 'Pending', 'Referred', 'Cancelled'])->default('Completed');
            $table->timestamps();

            $table->foreign('resident_id')->references('id')->on('residents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_logbooks');
    }
}; 