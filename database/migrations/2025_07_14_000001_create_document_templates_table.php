<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
    }

    public function down()
    {
        Schema::dropIfExists('document_templates');
    }
} 