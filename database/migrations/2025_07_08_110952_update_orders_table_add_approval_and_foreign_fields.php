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
    Schema::table('orders', function (Blueprint $table) {

        // if (!Schema::hasColumn('orders', 'promo_code_id')) {
        //     $table->unsignedBigInteger('promo_code_id')->nullable()->after('delivery_id');
        //     $table->foreign('promo_code_id')->references('id')->on('promo_codes')->nullOnDelete();
        // }

        if (!Schema::hasColumn('orders', 'address_id')) {
            $table->unsignedBigInteger('address_id')->nullable()->after('promo_code_id');
            $table->foreign('address_id')->references('id')->on('addresses')->nullOnDelete();
        }

        if (!Schema::hasColumn('orders', 'cart_id')) {
            $table->unsignedBigInteger('cart_id')->nullable()->after('address_id');
            $table->foreign('cart_id')->references('id')->on('carts')->nullOnDelete();
        }

        if (!Schema::hasColumn('orders', 'approved_by')) {
            $table->unsignedBigInteger('approved_by')->nullable()->after('cart_id');
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        }

        if (!Schema::hasColumn('orders', 'is_approved')) {
            $table->boolean('is_approved')->default(false)->after('total_price');
        }

        if (!Schema::hasColumn('orders', 'approved_at')) {
            $table->timestamp('approved_at')->nullable()->after('is_approved');
        }

        if (!Schema::hasColumn('orders', 'rejection_reason')) {
            $table->text('rejection_reason')->nullable()->after('approved_at');
        }

        if (!Schema::hasColumn('orders', 'approval_deadline')) {
            $table->timestamp('approval_deadline')->nullable()->after('rejection_reason');
        }
    });
}

    public function down(): void
    {
               Schema::table('orders', function (Blueprint $table) {
            // حذف الحقول
          //  $table->dropForeign(['promo_code_id']);
            $table->dropForeign(['address_id']);
            $table->dropForeign(['cart_id']);
            $table->dropForeign(['approved_by']);

            $table->dropColumn([
            //    'promo_code_id',
                'address_id',
                'cart_id',
                'approved_by',
                'is_approved',
                'approved_at',
                'rejection_reason',
                'approval_deadline',
            ]);
        });

    }
};
