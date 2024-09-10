<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobBatchesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('job_batches')) {
            Schema::create('job_batches', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('total_jobs');
                $table->integer('pending_jobs');
                $table->integer('failed_jobs');
                $table->longText('failed_job_ids');
                $table->text('options');
                $table->timestamp('cancelled_at')->nullable();
                $table->timestamps();
            });
        }
    }
    public function down()
    {
        Schema::dropIfExists('job_batches');
    }
}


