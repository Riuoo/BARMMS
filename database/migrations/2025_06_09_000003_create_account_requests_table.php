<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountRequestsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('account_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('barangay_profile_id')->nullable()->index();
            $table->string('email')->unique();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('suffix')->nullable();
            $table->string('status')->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->string('full_name')->nullable();
            $table->string('address')->nullable();
            $table->json('verification_documents')->nullable();
            $table->boolean('is_read')->default(false);
            $table->string('token')->nullable();
            $table->timestamps();

            $table->foreign('barangay_profile_id')->references('id')->on('barangay_profiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_requests');
    }
}
