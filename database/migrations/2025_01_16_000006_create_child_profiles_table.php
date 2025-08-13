<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('child_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('birth_date');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('birth_place')->nullable();
            $table->string('birth_certificate_number')->nullable();
            $table->string('mother_name');
            $table->string('father_name')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relationship')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('address');
            $table->string('barangay');
            $table->string('city');
            $table->string('province');
            $table->string('zip_code');
            $table->text('medical_conditions')->nullable();
            $table->text('allergies')->nullable();
            $table->text('special_notes')->nullable();
            $table->unsignedBigInteger('registered_by'); // ID of the admin who registered the child
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('registered_by')->references('id')->on('residents')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('child_profiles');
    }
};
