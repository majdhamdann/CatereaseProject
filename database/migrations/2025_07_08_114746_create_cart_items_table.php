<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('cart_id');
    $table->unsignedBigInteger('package_id');
    $table->unsignedBigInteger('occasion_type_id')->nullable();

    $table->integer('quantity')->default(1);
    $table->integer('extra_persons')->default(0);
    $table->decimal('total_price', 10, 2)->default(0.00);


    $table->timestamps();

    $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
    $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
    $table->foreign('occasion_type_id')->references('id')->on('occasion_types')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
