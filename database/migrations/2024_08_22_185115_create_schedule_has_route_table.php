<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleHasRouteTable extends Migration
{
    public function up()
    {
        Schema::create('schedule_has_route', function (Blueprint $table) {
            // Add the primary key `id`
            $table->bigIncrements('id')->unsigned();

            $table->unsignedBigInteger('ScheduleID');
            $table->unsignedBigInteger('RouteUpID');
            // $table->dateTime('StartPriceAt');
            // $table->dateTime('EndPriceAt');
            // $table->decimal('Price', 10, 2);
            $table->unsignedBigInteger('RouteDownID');
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('ScheduleID')->references('ScheduleID')->on('schedule')
                  ->onDelete('no action')->onUpdate('no action');
            $table->foreign('RouteUpID')->references('RouteID')->on('route_up')
                  ->onDelete('no action')->onUpdate('no action');
            $table->foreign('RouteDownID')->references('idRouteDown')->on('route_down')
                  ->onDelete('no action')->onUpdate('no action');
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedule_has_route');
    }
}