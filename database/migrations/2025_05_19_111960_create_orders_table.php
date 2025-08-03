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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('delivery_id')->nullable();
            $table->unsignedBigInteger('address_id')->nullable();
            $table->unsignedBigInteger('cart_id')->nullable();

            $table->enum('status', ['pending', 'confirmed', 'preparing', 'delivered', 'cancelled','assigned']);
            $table->boolean('is_submitted')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedBigInteger('promo_code_id')->nullable();
            $table->decimal('total_price', 10, 2);
            $table->boolean('prepayment_paid')->default(false);
            $table->timestamp('prepayment_paid_at')->nullable();

            $table->boolean('final_payment_paid')->default(false);
            $table->timestamp('final_payment_paid_at')->nullable();


            $table->text('notes')->nullable();

            $table->uuid('qr_token')->unique()->nullable();

            $table->timestamp('delivery_time')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
