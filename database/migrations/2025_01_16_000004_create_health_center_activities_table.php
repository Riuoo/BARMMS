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
        Schema::create('health_center_activities', function (Blueprint $table) {
            $table->id();
            $table->string('activity_name');
            $table->enum('activity_type', ['Vaccination Drive', 'Health Education', 'Medical Mission', 'Screening Program', 'Nutrition Program', 'Maternal Care', 'Child Care', 'Elderly Care', 'Dental Care', 'Mental Health', 'Other']);
            $table->date('activity_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('location');
            $table->text('description');
            $table->string('image')->nullable();
            $table->text('objectives')->nullable();
            $table->integer('target_participants')->nullable();
            $table->integer('actual_participants')->nullable();
            $table->string('organizer')->nullable();
            $table->text('materials_needed')->nullable();
            $table->decimal('budget', 10, 2)->nullable();
            $table->text('outcomes')->nullable();
            $table->text('challenges')->nullable();
            $table->text('recommendations')->nullable();
            $table->enum('status', ['Planned', 'Ongoing', 'Completed', 'Cancelled'])->default('Planned');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_center_activities');
    }
}; 