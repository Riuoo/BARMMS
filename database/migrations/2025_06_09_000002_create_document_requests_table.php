<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resident_id')->index();
            // Add template reference column at creation time; FK constraint is added in a later migration
            $table->foreignId('document_template_id')->nullable();
            $table->string('document_type');
            $table->text('description')->nullable();
            $table->json('additional_data')->nullable();
            $table->enum('status', ['pending', 'approved', 'completed'])->default('pending');
            $table->boolean('is_read')->default(false);
            $table->boolean('resident_is_read')->default(true);
            $table->timestamps();

            $table->foreign('resident_id')->references('id')->on('residents')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_requests');
    }
}
