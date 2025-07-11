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
         Schema::create('package_extras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('food_item_id')->nullable();
            $table->unsignedBigInteger('branch_service_type_id')->nullable();

            $table->enum('type', ['food_item', 'service', 'simple']);


            $table->string('name');
            $table->decimal('price', 10, 2)->default(0.00);
            $table->boolean('is_optional')->default(true);
            $table->timestamps();

            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->foreign('food_item_id')->references('id')->on('food_items')->nullOnDelete();
            $table->foreign('branch_service_type_id')->references('id')->on('branch_service_types')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_extras');
    }
};
