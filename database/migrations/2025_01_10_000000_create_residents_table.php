<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role');
            $table->string('address')->nullable();
            // Demographic fields
            $table->integer('age')->nullable();
            $table->integer('family_size')->nullable();
            $table->enum('education_level', [
                'No Education', 'Elementary', 'High School', 'Vocational', 'College', 'Post Graduate'
            ])->nullable();
            $table->enum('income_level', [
                'Low', 'Lower Middle', 'Middle', 'Upper Middle', 'High'
            ])->nullable();
            $table->enum('employment_status', [
                'Unemployed', 'Part-time', 'Self-employed', 'Full-time'
            ])->nullable();
            $table->enum('health_status', [
                'Critical', 'Poor', 'Fair', 'Good', 'Excellent'
            ])->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};