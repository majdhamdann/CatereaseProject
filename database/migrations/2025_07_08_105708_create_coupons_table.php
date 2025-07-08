<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('branch_id'); 
            $table->string('code')->unique(); 
            $table->decimal('discount_amount', 10, 2); 
            $table->date('expiration_date'); 
            $table->boolean('used')->default(false); 
            $table->unsignedBigInteger('promo_code_id')->nullable();

            $table->timestamps();

            // العلاقات
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('promo_code_id')->references('id')->on('promo_codes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
