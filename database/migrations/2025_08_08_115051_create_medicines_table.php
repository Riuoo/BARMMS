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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('generic_name')->nullable();
            $table->enum('category', ['Antibiotic', 'Pain Relief', 'Vitamins', 'Chronic', 'Emergency', 'Antihypertensive', 'Antidiabetic', 'Antihistamine', 'Other'])->default('Other');
            $table->text('description')->nullable();
            $table->string('dosage_form')->nullable(); // tablet, capsule, syrup, etc.
            $table->string('manufacturer')->nullable();
            $table->integer('current_stock')->default(0)->index();
            $table->integer('minimum_stock')->default(0)->index();
            $table->date('expiry_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['category', 'is_active']);
            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
