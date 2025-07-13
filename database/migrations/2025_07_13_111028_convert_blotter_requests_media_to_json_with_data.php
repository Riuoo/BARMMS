<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add a new JSON column
        Schema::table('blotter_requests', function (Blueprint $table) {
            $table->json('media_files')->nullable()->after('media');
        });

        // Convert existing media data to JSON format
        $blotterRequests = DB::table('blotter_requests')->whereNotNull('media')->get();
        
        foreach ($blotterRequests as $request) {
            if ($request->media) {
                $mediaArray = [
                    [
                        'name' => 'Attached File',
                        'path' => $request->media,
                        'type' => 'unknown',
                        'size' => 0,
                    ]
                ];
                
                DB::table('blotter_requests')
                    ->where('id', $request->id)
                    ->update(['media_files' => json_encode($mediaArray)]);
            }
        }

        // Drop the old media column
        Schema::table('blotter_requests', function (Blueprint $table) {
            $table->dropColumn('media');
        });

        // Rename media_files to media
        Schema::table('blotter_requests', function (Blueprint $table) {
            $table->renameColumn('media_files', 'media');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the old media column
        Schema::table('blotter_requests', function (Blueprint $table) {
            $table->string('media')->nullable()->after('description');
        });

        // Convert JSON data back to string format (take first file only)
        $blotterRequests = DB::table('blotter_requests')->whereNotNull('media')->get();
        
        foreach ($blotterRequests as $request) {
            if ($request->media) {
                $mediaArray = json_decode($request->media, true);
                if (!empty($mediaArray) && isset($mediaArray[0]['path'])) {
                    DB::table('blotter_requests')
                        ->where('id', $request->id)
                        ->update(['media' => $mediaArray[0]['path']]);
                }
            }
        }

        // Drop the JSON column
        Schema::table('blotter_requests', function (Blueprint $table) {
            $table->dropColumn('media');
        });
    }
};
