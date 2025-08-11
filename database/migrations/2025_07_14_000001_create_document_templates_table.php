<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDocumentTemplatesTable extends Migration
{
    public function up()
    {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('document_type');
            $table->text('header_content')->nullable();
            $table->text('body_content')->nullable();
            $table->text('footer_content')->nullable();
            $table->text('custom_css')->nullable();
            $table->json('placeholders')->nullable(); // Store available placeholders
            $table->json('settings')->nullable(); // Store template settings
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Each document type can only have one template
            $table->unique('document_type');
        });

        // After templates table exists, ensure the FK from document_requests -> document_templates is present on fresh installs
        Schema::table('document_requests', function (Blueprint $table) {
            // Add index if missing (wrapped in try to be idempotent)
            try {
                $table->index('document_template_id');
            } catch (\Throwable $e) {
                // ignore if index already exists
            }
            // Add foreign key if not already added by later migrations
            try {
                $table->foreign('document_template_id')->references('id')->on('document_templates');
            } catch (\Throwable $e) {
                // ignore if FK already exists
            }
        });

        // Best-effort backfill: only effective if templates and requests already exist at this point
        try {
            DB::statement(<<<SQL
                UPDATE document_requests dr
                JOIN document_templates dt
                  ON LOWER(TRIM(dr.document_type)) = LOWER(TRIM(dt.document_type))
                SET dr.document_template_id = dt.id
            SQL);
        } catch (\Throwable $e) {
            // ignore if statement fails in some environments
        }
    }

    public function down()
    {
        // First drop FK from document_requests -> document_templates to avoid FK constraint errors
        try {
            Schema::table('document_requests', function (Blueprint $table) {
                try {
                    $table->dropForeign(['document_template_id']);
                } catch (\Throwable $e) {
                    // ignore if FK does not exist
                }
                try {
                    $table->dropIndex(['document_template_id']);
                } catch (\Throwable $e) {
                    // ignore if index does not exist
                }
            });
        } catch (\Throwable $e) {
            // ignore table-not-found, etc.
        }

        Schema::dropIfExists('document_templates');
    }
} 