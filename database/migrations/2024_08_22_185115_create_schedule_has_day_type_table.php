<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleHasDayTypeTable extends Migration
{
    public function up()
    {
        Schema::create('schedule_has_day_type', function (Blueprint $table) {
            $table->unsignedBigInteger('ScheduleID');
            $table->unsignedBigInteger('RouteID');
            $table->unsignedBigInteger('DayTypeID');
            $table->dateTime('StartDateAt');
            $table->dateTime('EndDateAt');
            $table->primary(['ScheduleID', 'RouteID', 'DayTypeID']);

            $table->foreign('ScheduleID')->references('ScheduleID')->on('schedule')
                  ->onDelete('no action')->onUpdate('no action');
            $table->foreign('DayTypeID')->references('DayTypeID')->on('day_type')
                  ->onDelete('no action')->onUpdate('no action');
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedule_has_day_type');
    }
}
