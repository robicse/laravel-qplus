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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('store_id')->nullable();
            $table->string('user_type')->default('Admin');
            $table->string('name');
            $table->string('username');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('image')->nullable('not-found.webp');
            $table->integer('refer')->nullable();
            $table->integer('otp')->nullable();
            $table->ipAddress('ip_address')->default('101.2.160.0');
            $table->timestamp('last_login')->nullable();
			$table->tinyInteger('banned')->default(0);
			$table->text('banned_reason')->nullable();
			$table->tinyInteger('status')->default(0);
            $table->bigInteger('created_by_user_id')->default(1);
            $table->bigInteger('updated_by_user_id')->default(1);
            $table->softDeletes();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
