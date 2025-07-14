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
        Schema::create('package_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id');

            $table->decimal('value', 10, 2);
            $table->string('description');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_discounts');
    }
};
