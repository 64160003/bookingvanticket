<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->bigIncrements('id'); // Equivalent to `bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT`
                $table->string('name'); // Equivalent to `varchar(255) NOT NULL`
                $table->string('email')->unique(); // Equivalent to `varchar(255) NOT NULL` with a unique index
                $table->timestamp('email_verified_at')->nullable(); // Equivalent to `timestamp NULL DEFAULT NULL`
                $table->string('password'); // Equivalent to `varchar(255) NOT NULL`
                $table->string('remember_token', 100)->nullable(); // Equivalent to `varchar(100) DEFAULT NULL`
                $table->timestamps(); // Equivalent to `timestamp NULL DEFAULT NULL` for `created_at` and `updated_at`
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}