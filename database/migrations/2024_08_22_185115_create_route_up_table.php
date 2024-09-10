<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRouteUpTable extends Migration
{
    public function up()
    {
        Schema::create('route_up', function (Blueprint $table) {
            $table->id('RouteID');
            $table->string('Origin', 100);
            $table->integer('Active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('route_up');
    }
}
