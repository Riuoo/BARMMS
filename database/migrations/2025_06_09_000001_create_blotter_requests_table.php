<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlotterRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('blotter_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type');
            $table->text('description');
            $table->enum('status', ['pending', 'approved'])->default('pending');
            $table->string('media')->nullable()->comment('Path to image or video evidence');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('blotter_requests');
    }
}
