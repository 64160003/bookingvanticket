<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleTable extends Migration
{
    public function up()
    {
        Schema::create('schedule', function (Blueprint $table) {
            $table->unsignedBigInteger('ScheduleID')->autoIncrement(); // Make sure this matches
            $table->time('DepartureTime');
            $table->integer('Active');
            $table->timestamps();
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('schedule');
    }
}
