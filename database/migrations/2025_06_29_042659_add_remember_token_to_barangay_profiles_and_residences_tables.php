<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // For barangay_profiles table
    public function up(): void
    {
        Schema::table('barangay_profiles', function (Blueprint $table) {
            $table->rememberToken(); // Adds a nullable varchar(100) column
        });
    }

    /**
     * Reverse the migrations.
     */
    // For barangay_profiles table
    public function down(): void
    {
        Schema::table('barangay_profiles', function (Blueprint $table) {
            $table->dropRememberToken();
        });
    }
};
