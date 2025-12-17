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
        Schema::table('account_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('resident_id')->nullable()->after('barangay_profile_id');
            $table->index('resident_id');
            $table->foreign('resident_id')->references('id')->on('residents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_requests', function (Blueprint $table) {
            $table->dropForeign(['resident_id']);
            $table->dropIndex(['resident_id']);
            $table->dropColumn('resident_id');
        });
    }
};
