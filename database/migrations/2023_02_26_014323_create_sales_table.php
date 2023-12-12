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
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('voucher_date',100)->nullable();
            $table->bigInteger('store_id')->unsigned()->nullable();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->bigInteger('order_type_id')->unsigned()->nullable();
            $table->foreign('order_type_id')->references('id')->on('order_types')->onDelete('cascade');
            $table->string('payment_type_id')->nullable();
            $table->float('total_quantity',16,2)->default(0);
            $table->float('sub_total', 16, 2)->default(0);
            $table->enum('discount_type', ['Flat', 'Percent'])->nullable();
            $table->float('discount_percent', 16, 2)->nullable();
            $table->float('discount_amount',16,2)->nullable();
            $table->float('after_discount', 16, 2)->nullable();
            $table->float('total_vat', 16, 2)->nullable();
            $table->float('grand_total',16,2)->default(0);
            $table->float('advance_minus_amount',16,2)->nullable();
            $table->float('paid_amount',16,2)->default(0);
            $table->float('due_amount',16,2)->default(0);
            $table->float('profit_amount',16,2)->default(0);
            $table->string('bank_name',100)->nullable();
            $table->string('cheque_number',100)->nullable();
            $table->string('cheque_date',50)->nullable();
            $table->string('transaction_number',50)->nullable();
            $table->string('note',255)->nullable();
            $table->string('hc_voucher_number',50)->nullable();
            $table->text('comments',255)->nullable();
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
        Schema::dropIfExists('sales');
    }
};
