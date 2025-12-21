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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['employment', 'health', 'education', 'social', 'safety', 'custom']);
            $table->text('description')->nullable();
            $table->json('criteria')->comment('Decision tree criteria in JSON format');
            $table->json('target_puroks')->nullable()->comment('Specific puroks to target (null = all puroks)');
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0)->comment('Priority level for recommendations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
