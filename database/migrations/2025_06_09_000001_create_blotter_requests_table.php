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
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('recipient_name')->nullable();
            $table->string('type');
            $table->text('description');
            $table->enum('status', ['pending', 'approved', 'completed'])->default('pending');
            $table->string('media')->nullable()->comment('Path to image or video evidence');
            $table->timestamps();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('summon_date')->nullable();
            $table->integer('attempts')->default(0);
            $table->dateTime('completed_at')->nullable();

            $table->foreign('user_id')->references('id')->on('residents')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('blotter_requests');
    }
}
