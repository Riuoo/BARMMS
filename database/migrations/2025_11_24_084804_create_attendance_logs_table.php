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
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resident_id')->index();
            $table->unsignedBigInteger('event_id')->nullable()->index();
            $table->string('event_type')->nullable(); // 'event', 'health_center_activity', 'medical_consultation', 'medicine_claim', etc.
            $table->unsignedBigInteger('scanned_by')->nullable()->index(); // Staff/admin who scanned
            $table->timestamp('scanned_at')->useCurrent()->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('resident_id')->references('id')->on('residents')->onDelete('cascade');
            $table->foreign('scanned_by')->references('id')->on('barangay_profiles')->onDelete('set null');
            
            // Prevent duplicate scans for same event (same day)
            $table->unique(['resident_id', 'event_id', 'event_type'], 'unique_attendance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
