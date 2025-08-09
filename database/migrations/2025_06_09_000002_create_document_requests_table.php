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
            $table->unsignedBigInteger('user_id')->index();
            $table->string('document_type');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'approved', 'completed'])->default('pending');
            $table->boolean('is_read')->default(false);
            $table->boolean('resident_is_read')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('residents')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_requests');
    }
}
