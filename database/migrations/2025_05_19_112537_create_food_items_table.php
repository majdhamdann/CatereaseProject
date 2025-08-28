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
        Schema::create('food_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
           // $table->unsignedBigInteger('food_category_id')->nullable();

            $table->string('name');
          // $table->text('description')->nullable();
           // $table->decimal('price', 10, 2);
          //  $table->decimal('discount_price', 10, 2)->nullable();
           // $table->longText('photo')->nullable();
            $table->boolean('available')->default(true);
            //$table->enum('type', ['veg', 'non_veg'])->default('non_veg');
           // $table->integer('calories')->nullable();
            $table->enum('type', ['veg', 'non_veg'])->nullable()->default(null);


            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
           // $table->foreign('food_category_id')->references('id')->on('food_categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_items');
    }
};
