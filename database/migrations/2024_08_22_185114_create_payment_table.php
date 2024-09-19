<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTable extends Migration
{
    public function up()
    {
        Schema::create('payment', function (Blueprint $table) {
            $table->id('PaymentID');
            $table->string('PaymentMethod', 45);
            $table->decimal('Amount', 10, 2);
            $table->unsignedBigInteger('BookingID');
            $table->tinyInteger('PaymentStatus');
            $table->timestamps();

            $table->foreign('BookingID')->references('BookingID')->on('booking')
                  ->onDelete('no action')->onUpdate('no action');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment');
    }
}
