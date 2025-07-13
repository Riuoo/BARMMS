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
        Schema::create('health_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('concern_type');
            $table->enum('severity', ['Mild', 'Moderate', 'Severe', 'Emergency']);
            $table->text('description');
            $table->string('contact_number')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'in_progress', 'resolved'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('residents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_statuses');
    }
};
