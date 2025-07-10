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
    Schema::create('packages', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('branch_id');
       // $table->unsignedBigInteger('category_id')->nullable();
        $table->unsignedBigInteger('service_type_id')->nullable();
        $table->unsignedBigInteger('occasion_type_id')->nullable();

        $table->string('name');
        $table->text('description')->nullable();
        $table->longText('photo')->nullable();
        $table->decimal('base_price', 10, 2)->default(0.00);
        $table->integer('serves_count')->default(0);

        $table->text('cancellation_policy')->nullable();
        $table->boolean('prepayment_required')->default(false);
        $table->decimal('prepayment_amount', 10, 2)->nullable();
        $table->boolean('is_active')->default(true);
        $table->text('notes')->nullable();

        $table->timestamps();


        $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
       // $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
        $table->foreign('service_type_id')->references('id')->on('service_types')->nullOnDelete();
        $table->foreign('occasion_type_id')->references('id')->on('occasion_types')->nullOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
