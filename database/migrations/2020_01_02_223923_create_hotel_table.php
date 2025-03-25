<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->default(0);
            $table->integer('status_id')->unsigned()->default(0);
            $table->string('brand_id')->nullable();
            $table->string('invoice')->nullable();
            $table->string('dimension')->nullable();
            $table->string('type')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('reg')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->text('message')->nullable();
            $table->string('condition_lp')->nullable();
            $table->string('condition_dp')->nullable();
            $table->string('condition_lz')->nullable();
            $table->string('condition_dz')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('paid')->default(false);
            $table->boolean('active')->default(false);
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
        Schema::dropIfExists('hotel');
    }
}



