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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->bigInteger('unit_id')->unsigned()->nullable();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->bigInteger('vat_id')->unsigned()->nullable();
            $table->foreign('vat_id')->references('id')->on('vats')->onDelete('cascade');
            $table->string('name');
            $table->string('barcode')->nullable();
            $table->integer('stock_low_qty')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->bigInteger('created_by_user_id')->unsigned()->nullable();
            $table->foreign('created_by_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('updated_by_user_id')->unsigned()->nullable();
            $table->foreign('updated_by_user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('products');
    }
};
