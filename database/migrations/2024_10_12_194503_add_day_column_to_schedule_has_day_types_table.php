<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDayColumnToScheduleHasDayTypesTable extends Migration
{
    public function up()
    {
        Schema::table('schedule_has_day_type', function (Blueprint $table) {
            $table->string('Day')->nullable();
        });
    }

    public function down()
    {
        Schema::table('schedule_has_day_type', function (Blueprint $table) {
            $table->dropColumn('Day');
        });
    }
}