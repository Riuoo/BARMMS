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
            $table->text('complaint')->nullable();
            $table->string('blood_pressure')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->integer('pulse_rate')->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('prescribed_medications')->nullable();
            $table->text('notes')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->text('follow_up_notes')->nullable();
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