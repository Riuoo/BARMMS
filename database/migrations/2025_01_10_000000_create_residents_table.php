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
            $table->string('name')->index();
            $table->string('email')->unique()->index();
            $table->string('password');
            $table->string('role');
            $table->string('address')->index();
            // Demographic fields
            $table->integer('age');
            $table->integer('family_size');
            $table->enum('education_level', ['No Education', 'Elementary', 'High School', 'Vocational', 'College', 'Post Graduate']);
            $table->enum('income_level', ['Low', 'Lower Middle', 'Middle', 'Upper Middle', 'High']);
            $table->enum('employment_status', ['Unemployed', 'Part-time', 'Self-employed', 'Full-time'])->default('Unemployed');
            $table->enum('health_status', ['Critical', 'Poor', 'Fair', 'Good', 'Excellent'])->default('Fair');
            $table->boolean('active')->default(true)->index();
            $table->rememberToken();
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};