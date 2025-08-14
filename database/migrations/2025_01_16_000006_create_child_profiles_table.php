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
			$table->date('birth_date');
			$table->enum('gender', ['Male', 'Female', 'Other']);
			$table->string('mother_name');
			$table->string('contact_number')->nullable();
			$table->string('purok');
			$table->unsignedBigInteger('registered_by');
			$table->boolean('is_active')->default(true);
			$table->timestamps();
			
			$table->foreign('registered_by')->references('id')->on('barangay_profiles')->onDelete('cascade');
		});
    }

    public function down(): void
    {
        Schema::dropIfExists('child_profiles');
    }
};
