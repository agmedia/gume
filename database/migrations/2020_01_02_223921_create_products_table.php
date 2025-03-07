<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
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
            $table->bigInteger('brand_id')->unsigned()->default(0);
            $table->bigInteger('action_id')->unsigned()->default(0);
            $table->string('sku', 14)->default(0)->index();
            $table->string('ean', 14)->nullable();
            $table->string('name')->index();
            $table->text('description')->nullable();
            $table->string('slug');
            $table->string('url', 255);
            $table->string('category_string', 255);
            $table->string('image')->nullable();
            $table->decimal('price', 15, 4)->default(0);
            $table->integer('quantity')->unsigned()->default(0);
            $table->tinyInteger('decrease')->unsigned()->default(1);
            $table->integer('tax_id')->unsigned()->default(0);
            $table->decimal('special', 15, 4)->nullable();
            $table->tinyInteger('special_lock')->nullable()->default(0);
            $table->timestamp('special_from')->nullable();
            $table->timestamp('special_to')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('related_products')->nullable();
            $table->integer('viewed')->unsigned()->default(0);
            $table->integer('sort_order')->unsigned()->default(0);
            $table->boolean('featured')->default(false);
            $table->boolean('status')->default(false);
            $table->timestamps();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('product_id')->unsigned()->index();
            $table->string('image');
            $table->string('alt')->nullable();
            $table->boolean('published')->default(false);
            $table->integer('sort_order')->unsigned();
            $table->timestamps();
        });

        Schema::create('product_actions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('type');
            $table->decimal('discount', 15, 4);
            $table->string('group');
            $table->text('links')->nullable();
            $table->timestamp('date_start')->nullable();
            $table->timestamp('date_end')->nullable();
            $table->text('data')->nullable();
            $table->decimal('min_cart', 15, 4)->nullable();
            $table->string('coupon', 15)->nullable();
            $table->boolean('logged')->default(0);
            $table->integer('quantity')->unsigned()->default(0);
            $table->tinyInteger('lock')->nullable()->default(0);
            $table->integer('uses_customer')->unsigned()->default(1);
            $table->integer('viewed')->unsigned()->default(0);
            $table->integer('clicked')->unsigned()->default(0);
            $table->boolean('status')->default(0);
            $table->timestamps();
        });

        Schema::create('product_category', function (Blueprint $table) {
            $table->integer('product_id')->unsigned()->index();
            $table->integer('category_id')->unsigned()->index();
        });

        Schema::create('product_attribute', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('attribute_id')->index();
            $table->string('value');
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
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_actions');
        Schema::dropIfExists('product_category');
        Schema::dropIfExists('product_attribute');
    }
}



