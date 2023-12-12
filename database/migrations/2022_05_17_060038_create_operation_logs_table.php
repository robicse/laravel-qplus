<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operation_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('date',100)->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('store_id')->nullable();
            $table->string('module',100)->nullable();
            $table->string('action',100)->nullable();
            $table->json('previous_data')->nullable();
            $table->json('current_data')->nullable();
            $table->string('status',100)->nullable();
            $table->bigInteger('activity_id')->unsigned()->nullable();
            $table->longText('remarks')->nullable();
            $table->text('device')->nullable();
            $table->string('ip',100)->nullable();
            $table->string('location',100)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operation_logs');
    }
};
