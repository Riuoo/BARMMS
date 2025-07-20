<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('community_complaints', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('title');
            $table->enum('category', [
                'Water Supply',
                'Electricity',
                'Roads & Infrastructure',
                'Garbage Collection',
                'Street Lighting',
                'Drainage & Sewage',
                'Noise Pollution',
                'Air Pollution',
                'Public Safety',
                'Health & Sanitation',
                'Transportation',
                'Other'
            ]);
            $table->text('description');
            $table->string('location')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'under_review', 'in_progress', 'resolved', 'closed'])->default('pending');
            $table->json('media')->nullable();
            $table->boolean('is_read')->default(false);
            $table->text('admin_notes')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->dateTime('assigned_at')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('residents')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('community_complaints');
    }
}; 