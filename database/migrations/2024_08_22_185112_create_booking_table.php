<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingTable extends Migration
{
    public function up()
    {
        Schema::create('booking', function (Blueprint $table) {
            $table->id('BookingID');
            $table->integer('Seat');
            $table->dateTime('BookingDate');
            $table->dateTime('TravelDate');
            $table->string('Name', 100);
            $table->string('Phone', 15);
            $table->string('System', 45);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('booking');
    }
}

