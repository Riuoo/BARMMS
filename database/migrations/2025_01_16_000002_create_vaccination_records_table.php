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
            $table->unsignedBigInteger('resident_id');
            $table->string('vaccine_name');
            $table->enum('vaccine_type', ['COVID-19', 'Influenza', 'Pneumonia', 'Tetanus', 'Hepatitis B', 'MMR', 'Varicella', 'HPV', 'Other']);
            $table->date('vaccination_date');
            $table->string('batch_number')->nullable();
            $table->string('manufacturer')->nullable();
            $table->integer('dose_number')->default(1);
            $table->date('next_dose_date')->nullable();
            $table->string('administered_by')->nullable();
            $table->text('side_effects')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('resident_id')->references('id')->on('residents')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaccination_records');
    }
}; 