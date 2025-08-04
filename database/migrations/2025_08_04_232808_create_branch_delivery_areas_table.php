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
        Schema::create('branch_delivery_areas', function (Blueprint $table) {
           $table->id();

           $table->unsignedBigInteger('branch_id');
           $table->unsignedBigInteger('city_id'); 

           $table->decimal('delivery_price', 10, 2);

           $table->timestamps();

           $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
           $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');

           $table->unique(['branch_id', 'city_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_delivery_areas');
    }
};
