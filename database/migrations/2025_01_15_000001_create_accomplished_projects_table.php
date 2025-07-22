<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accomplished_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->string('location')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->date('start_date');
            $table->date('completion_date');
            $table->string('status')->default('completed');
            $table->string('image')->nullable();
            $table->text('beneficiaries')->nullable();
            $table->text('impact')->nullable();
            $table->string('funding_source')->nullable();
            $table->string('implementing_agency')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accomplished_projects');
    }
}; 