<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRouteDownTable extends Migration
{
    public function up()
    {
        Schema::create('route_down', function (Blueprint $table) {
            $table->id('idRouteDown');
            $table->string('Destination', 45);
            $table->decimal('Price', 10, 2);
            $table->integer('Active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('route_down');
    }
}
