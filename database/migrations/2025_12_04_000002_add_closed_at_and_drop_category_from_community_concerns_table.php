<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('community_concerns', function (Blueprint $table) {
            if (!Schema::hasColumn('community_concerns', 'closed_at')) {
                $table->dateTime('closed_at')->nullable()->after('resolved_at');
            }
            if (Schema::hasColumn('community_concerns', 'category')) {
                $table->dropColumn('category');
            }
        });
    }

    public function down()
    {
        Schema::table('community_concerns', function (Blueprint $table) {
            if (Schema::hasColumn('community_concerns', 'closed_at')) {
                $table->dropColumn('closed_at');
            }
            if (!Schema::hasColumn('community_concerns', 'category')) {
                $table->enum('category', [
                    'Water Supply',
                    'Electricity',
                    'Roads & Infrastructure',
                    'Garbage Collection',
                    'Street Lighting',
                    'Drainage & Sewage',
                    'Noise Pollution',
                    'Air Pollution',
                    'Public Safety',
                    'Health & Sanitation',
                    'Transportation',
                    'Other',
                ])->nullable();
            }
        });
    }
};


