<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRouteHistoryTable extends Migration
{
    public function up()
    {
        Schema::create('route_history', function (Blueprint $table) {
            $table->id('RouteHistoryID');
            $table->decimal('OldPrice', 10, 2);
            $table->decimal('NewPrice', 10, 2);
            $table->string('OldDestination', 100);
            $table->dateTime('NewDestination');
            $table->string('ModifiedTime', 45);
            $table->unsignedBigInteger('RouteID');
            $table->timestamps();

            $table->foreign('RouteID')->references('RouteID')->on('route_up')
                  ->onDelete('no action')->onUpdate('no action');
        });
    }

    public function down()
    {
        Schema::dropIfExists('route_history');
    }
}
