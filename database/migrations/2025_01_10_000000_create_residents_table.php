<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->string('qr_code_token')->unique()->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->string('email')->nullable();
            $table->string('password');
            $table->text('two_factor_secret')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            $table->timestamp('two_factor_enabled_at')->nullable();
            $table->string('role');
            $table->string('address')->index();
            
            // New personal information fields
            $table->enum('gender', ['Male', 'Female']);
            $table->string('contact_number')->nullable();
            $table->date('birth_date');
            $table->enum('marital_status', ['Single', 'Married', 'Widowed', 'Divorced', 'Separated']);
            $table->string('occupation')->nullable();
            
            // Demographic fields
            $table->integer('age');
            $table->integer('family_size');
            $table->enum('education_level', ['No Education', 'Elementary', 'High School', 'Vocational', 'College', 'Post Graduate']);
            $table->enum('income_level', ['Low', 'Lower Middle', 'Middle', 'Upper Middle', 'High']);
            $table->enum('employment_status', ['Unemployed', 'Part-time', 'Self-employed', 'Full-time'])->default('Unemployed');
            $table->boolean('is_pwd')->default(false);
            
            // Emergency contact fields
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_number')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            
            $table->boolean('active')->default(true)->index();
            $table->rememberToken();
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        // Dynamically find and drop all foreign key constraints that reference residents
        // This handles auto-generated constraint names from foreignId()->constrained()
        
        $dbName = DB::connection()->getDatabaseName();
        
        // Get all foreign keys that reference residents
        $foreignKeys = DB::select("
            SELECT 
                CONSTRAINT_NAME,
                TABLE_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE REFERENCED_TABLE_SCHEMA = ?
            AND REFERENCED_TABLE_NAME = 'residents'
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
        
        Schema::dropIfExists('residents');
    }
};