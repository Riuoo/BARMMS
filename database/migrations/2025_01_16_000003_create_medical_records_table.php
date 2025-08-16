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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resident_id')->index();
            $table->unsignedBigInteger('attending_health_worker_id')->index();
            $table->dateTime('consultation_datetime')->useCurrent()->index();
            $table->string('consultation_type');
            $table->text('chief_complaint')->nullable();
            $table->text('symptoms')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('prescribed_medications')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->integer('blood_pressure_systolic')->nullable();
            $table->integer('blood_pressure_diastolic')->nullable();
            $table->integer('pulse_rate')->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->decimal('height_cm', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->timestamps();
            $table->index('created_at');

            $table->foreign('resident_id')->references('id')->on('residents')->onDelete('cascade');
            $table->foreign('attending_health_worker_id')->references('id')->on('barangay_profiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
}; 