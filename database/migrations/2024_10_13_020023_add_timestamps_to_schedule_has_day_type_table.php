<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimestampsToScheduleHasDayTypeTable extends Migration
{
    public function up()
    {
        Schema::table('schedule_has_day_type', function (Blueprint $table) {
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('schedule_has_day_type', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
}