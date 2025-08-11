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
        Schema::create('password_resets', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->unsignedBigInteger('resident_id')->nullable();
            $table->unsignedBigInteger('barangay_profile_id')->nullable();

            // Core fields
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();

            // Constraints and indexes
            $table->foreign('resident_id')
                ->references('id')
                ->on('residents')
                ->onDelete('cascade');

            $table->foreign('barangay_profile_id')
                ->references('id')
                ->on('barangay_profiles')
                ->onDelete('cascade');

            $table->index(['resident_id', 'barangay_profile_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_resets');
    }
};
