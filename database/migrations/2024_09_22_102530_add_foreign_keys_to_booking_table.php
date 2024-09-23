<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToBookingTable extends Migration
{
    public function up()
    {
        Schema::table('booking', function (Blueprint $table) {
            $table->unsignedBigInteger('RouteUpID')->nullable()->after('System');
            $table->unsignedBigInteger('RouteDownID')->nullable()->after('RouteUpID');
            $table->unsignedBigInteger('ScheduleID')->nullable()->after('RouteDownID');

            $table->foreign('RouteUpID')->references('RouteID')->on('route_up')->onDelete('set null');
            $table->foreign('RouteDownID')->references('idRouteDown')->on('route_down')->onDelete('set null');
            $table->foreign('ScheduleID')->references('ScheduleID')->on('schedule')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('booking', function (Blueprint $table) {
            $table->dropForeign(['RouteUpID']);
            $table->dropForeign(['RouteDownID']);
            $table->dropForeign(['ScheduleID']);

            $table->dropColumn(['RouteUpID', 'RouteDownID', 'ScheduleID']);
        });
    }
}