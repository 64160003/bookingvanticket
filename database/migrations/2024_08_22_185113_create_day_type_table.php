<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDayTypeTable extends Migration
{
    public function up()
    {
        Schema::create('day_type', function (Blueprint $table) {
            $table->id('DayTypeID');
            $table->string('DayTypeName', 50);
            $table->integer('Active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('day_type');
    }
}
