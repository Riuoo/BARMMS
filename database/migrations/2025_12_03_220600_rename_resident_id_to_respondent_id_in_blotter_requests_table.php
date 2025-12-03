<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Note:
     * This migration is now effectively a no-op because
     * `respondent_id` is defined directly in the original
     * `create_blotter_requests_table` migration.
     *
     * Keeping this file avoids issues on projects that
     * already ran it, while fresh installs won't need it.
     */
    public function up(): void
    {
        // No changes needed; schema is defined in 2025_06_09_000001_create_blotter_requests_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback actions; schema is handled by the original create table migration
    }
};
