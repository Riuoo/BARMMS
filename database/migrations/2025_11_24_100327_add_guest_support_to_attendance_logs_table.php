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
        Schema::table('attendance_logs', function (Blueprint $table) {
            // Drop existing unique constraint
            $table->dropUnique('unique_attendance');
            
            // Make resident_id nullable
            $table->unsignedBigInteger('resident_id')->nullable()->change();
            
            // Add guest fields
            $table->string('guest_name')->nullable()->after('resident_id');
            $table->string('guest_contact')->nullable()->after('guest_name');
            
            // Add new unique constraints
            // For residents: same resident can't attend same event twice
            $table->unique(['resident_id', 'event_id', 'event_type'], 'unique_resident_attendance');
            
            // For guests: same name can't attend same event on the same day
            // Note: We'll handle this in application logic since we need to check by date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            // Drop new constraints
            $table->dropUnique('unique_resident_attendance');
            
            // Remove guest fields
            $table->dropColumn(['guest_name', 'guest_contact']);
            
            // Make resident_id not nullable again
            $table->unsignedBigInteger('resident_id')->nullable(false)->change();
            
            // Restore original unique constraint
            $table->unique(['resident_id', 'event_id', 'event_type'], 'unique_attendance');
        });
    }
};
