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
        Schema::create('stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('purchase_id')->unsigned()->nullable();
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->bigInteger('store_id')->unsigned()->nullable();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->bigInteger('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->bigInteger('unit_id')->unsigned()->nullable();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->bigInteger('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->integer('qty')->default(0);
            $table->integer('already_return_qty')->default(0);
            $table->float('purchase_price',16,2)->default(0);
            $table->enum('product_discount_type', ['Flat','Percent']);
            $table->float('product_discount_percent',16,2)->nullable()->default(0);
            $table->float('product_discount',16,2)->default(0);
            $table->float('after_product_discount',16,2)->default(0);
            $table->float('product_vat',16,2)->nullable()->default(0);
            $table->float('product_vat_amount',16,2)->default(0);
            $table->float('product_total',16,2)->default(0);
            $table->float('sale_price',16,2)->default(0);
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
        Schema::dropIfExists('stocks');
    }
};
