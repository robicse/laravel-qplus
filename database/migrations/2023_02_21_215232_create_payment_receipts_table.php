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
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('date',100);
            $table->bigInteger('store_id')->unsigned()->nullable();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->enum('order_type',['Purchase','Sale','Purchase Return','Sale Return'])->nullable();
            $table->string('order_id')->nullable();
            $table->bigInteger('supplier_id')->unsigned()->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->bigInteger('order_type_id')->unsigned()->nullable();
            $table->foreign('order_type_id')->references('id')->on('order_types')->onDelete('cascade');
            $table->string('payment_type_id')->nullable();
            $table->float('total',16,2)->default(0);
            $table->float('amount',16,2)->default(0);
            $table->float('advance_minus_amount',16,2)->default(0);
            $table->float('due',16,2)->default(0);
            $table->float('exchange',16,2)->default(0);
            $table->string('comments',255)->nullable();
            $table->string('bank_name',100)->nullable();
            $table->string('cheque_number',100)->nullable();
            $table->string('cheque_date',50)->nullable();
            $table->string('transaction_number',50)->nullable();
            $table->string('note',255)->nullable();
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
        Schema::dropIfExists('payment_receipts');
    }
};
