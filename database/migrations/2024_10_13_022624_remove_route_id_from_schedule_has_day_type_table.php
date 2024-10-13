<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveRouteIdFromScheduleHasDayTypeTable extends Migration
{
    public function up()
    {
        Schema::table('schedule_has_day_type', function (Blueprint $table) {
            // Drop the foreign key constraints related to RouteID and the primary key
            $table->dropForeign(['DayTypeID']); // drop foreign key on DayTypeID
            $table->dropForeign(['ScheduleID']); // drop foreign key on ScheduleID

            // Drop the primary key
            $table->dropPrimary(['ScheduleID', 'RouteID', 'DayTypeID']);

            // Drop the RouteID column
            $table->dropColumn('RouteID');

            // Re-add the primary key without RouteID
            $table->primary(['ScheduleID', 'DayTypeID']);

            // Re-add foreign key constraints
            $table->foreign('ScheduleID')->references('ScheduleID')->on('schedule');
            $table->foreign('DayTypeID')->references('DayTypeID')->on('day_type');
        });
    }

    public function down()
    {
        Schema::table('schedule_has_day_type', function (Blueprint $table) {
            // Add RouteID column back
            $table->unsignedBigInteger('RouteID')->nullable();

            // Drop the current primary key
            $table->dropPrimary(['ScheduleID', 'DayTypeID']);

            // Add the original primary key back including RouteID
            $table->primary(['ScheduleID', 'RouteID', 'DayTypeID']);

            // Re-add foreign key constraints
            $table->foreign('ScheduleID')->references('ScheduleID')->on('schedule');
            $table->foreign('DayTypeID')->references('DayTypeID')->on('day_type');
            $table->foreign('RouteID')->references('RouteID')->on('route_up');
        });
    }
}
