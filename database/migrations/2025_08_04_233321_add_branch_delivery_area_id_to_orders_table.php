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
           $table->unsignedBigInteger('branch_delivery_area_id')->nullable()->after('branch_id');

           $table->foreign('branch_delivery_area_id')
             ->references('id')->on('branch_delivery_areas')
             ->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
           $table->dropForeign(['branch_delivery_area_id']);
           $table->dropColumn('branch_delivery_area_id');
        });

    }
};
