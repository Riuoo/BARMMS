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
        Schema::create('accomplished_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('category'); // Infrastructure, Health, Education, etc.
            $table->string('location')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->date('start_date');
            $table->date('completion_date');
            $table->string('status')->default('completed');
            $table->string('image')->nullable(); // Path to project image
            $table->text('beneficiaries')->nullable(); // Who benefited from the project
            $table->text('impact')->nullable(); // Impact on the community
            $table->string('funding_source')->nullable(); // Government, NGO, etc.
            $table->string('implementing_agency')->nullable();
            $table->boolean('is_featured')->default(false); // For highlighting on landing page
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accomplished_projects');
    }
}; 