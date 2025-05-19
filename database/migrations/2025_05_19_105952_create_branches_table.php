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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Restaurant_id');
            $table->string('location');
            $table->text('description')->nullable();
            $table->longText('logo_url')->nullable();
            $table->unsignedBigInteger('Manager_id')->nullable();

            $table->foreign('Restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
             $table->foreign('Manager_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
