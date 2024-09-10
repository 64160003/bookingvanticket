<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingScheduleDetailTable extends Migration
{
    public function up()
    {
        Schema::create('booking_schedule_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('ScheduleID');
            $table->unsignedBigInteger('BookingID');
            $table->timestamps();

            $table->foreign('ScheduleID')->references('ScheduleID')->on('schedule')
                  ->onDelete('no action')->onUpdate('no action');
            $table->foreign('BookingID')->references('BookingID')->on('booking')
                  ->onDelete('no action')->onUpdate('no action');
        });
    }

    public function down()
    {
        Schema::dropIfExists('booking_schedule_detail');
    }
}
