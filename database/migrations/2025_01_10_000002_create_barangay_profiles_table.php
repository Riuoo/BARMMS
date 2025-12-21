<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangay_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->text('two_factor_secret')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            $table->timestamp('two_factor_enabled_at')->nullable();
            $table->string('role');
            $table->string('address');
            $table->string('contact_number')->nullable();
            $table->boolean('active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Dynamically find and drop all foreign key constraints that reference barangay_profiles
        // This handles auto-generated constraint names from foreignId()->constrained()
        
        $dbName = DB::connection()->getDatabaseName();
        
        // Get all foreign keys that reference barangay_profiles
        $foreignKeys = DB::select("
            SELECT 
                CONSTRAINT_NAME,
                TABLE_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE REFERENCED_TABLE_SCHEMA = ?
            AND REFERENCED_TABLE_NAME = 'barangay_profiles'
        ", [$dbName]);
        
        // Drop each foreign key constraint
        foreach ($foreignKeys as $fk) {
            try {
                DB::statement("ALTER TABLE `{$fk->TABLE_NAME}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            } catch (\Exception $e) {
                // Ignore errors if constraint doesn't exist or table doesn't exist
                // This can happen during fresh migrations
            }
        }
        
        Schema::dropIfExists('barangay_profiles');
    }
};
