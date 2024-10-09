<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedToRouteUpTable extends Migration
{
    public function up()
    {
        Schema::table('route_up', function (Blueprint $table) {
            $table->boolean('Deleted')->default(false);
        });
    }

    public function down()
    {
        Schema::table('route_up', function (Blueprint $table) {
            $table->dropColumn('Deleted');
        });
    }
}