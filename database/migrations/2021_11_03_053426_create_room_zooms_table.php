<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomZoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoom_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('meeting_id');
            $table->string('host_id');
            $table->string('host_email');
            $table->string('topic');
            $table->string('status');
            $table->string('start_time');
            $table->integer('duration');
            $table->string('timezone');
            $table->text('start_url');
            $table->text('join_url');
            $table->text('password');
            $table->text('h323_password');
            $table->text('pstn_password');
            $table->text('encrypted_password');
            $table->boolean('pre_schedule');
            $table->integer('quota');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_zooms');
    }
}
