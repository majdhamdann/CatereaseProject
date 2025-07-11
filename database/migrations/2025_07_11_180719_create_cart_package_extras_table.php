<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartPackageExtrasTable extends Migration
{
    public function up()
    {
        Schema::create('cart_package_extras', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cart_item_id');
            $table->unsignedBigInteger('extra_id');
            $table->integer('quantity')->default(1);
            $table->decimal('total_price', 10, 2)->default(0.00);

            $table->timestamps();

            $table->foreign('cart_item_id')->references('id')->on('cart_items')->onDelete('cascade');
            $table->foreign('extra_id')->references('id')->on('package_extras')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cart_package_extras');
    }
}
