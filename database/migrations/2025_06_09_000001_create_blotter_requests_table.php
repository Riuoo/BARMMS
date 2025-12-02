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
            $table->string('complainant_name')->nullable();
            $table->unsignedBigInteger('resident_id')->nullable()->index();
            $table->string('type');
            $table->text('description');
            $table->enum('status', ['pending', 'approved', 'completed'])->default('pending');
            $table->boolean('is_read')->default(false);
            $table->boolean('resident_is_read')->default(true);
            $table->json('media')->nullable()->comment('Path(s) to image or video evidence');
            $table->timestamps();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('summon_date')->nullable();
            $table->integer('attempts')->default(0);
            $table->dateTime('completed_at')->nullable();

            $table->foreign('resident_id')->references('id')->on('residents')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('blotter_requests');
    }
}
