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
        Schema::table('blotter_requests', function (Blueprint $table) {
            $table->boolean('is_read')->default(false)->after('status');
        });
        Schema::table('document_requests', function (Blueprint $table) {
            $table->boolean('is_read')->default(false)->after('status');
        });
        Schema::table('account_requests', function (Blueprint $table) {
            $table->boolean('is_read')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blotter_requests', function (Blueprint $table) {
            $table->dropColumn('is_read');
        });
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropColumn('is_read');
        });
        Schema::table('account_requests', function (Blueprint $table) {
            $table->dropColumn('is_read');
        });
    }
};

