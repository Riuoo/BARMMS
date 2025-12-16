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
        Schema::table('health_center_activities', function (Blueprint $table) {
            $table->string('audience_scope')->default('all')->after('status');
            $table->string('audience_purok')->nullable()->after('audience_scope');
            $table->boolean('reminder_sent')->default(false)->after('audience_purok');
        });

        Schema::table('accomplished_projects', function (Blueprint $table) {
            $table->string('audience_scope')->default('all')->after('status');
            $table->string('audience_purok')->nullable()->after('audience_scope');
            $table->boolean('reminder_sent')->default(false)->after('audience_purok');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_center_activities', function (Blueprint $table) {
            $table->dropColumn(['audience_scope', 'audience_purok', 'reminder_sent']);
        });

        Schema::table('accomplished_projects', function (Blueprint $table) {
            $table->dropColumn(['audience_scope', 'audience_purok', 'reminder_sent']);
        });
    }
};


