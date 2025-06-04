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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('FeedbackType_id');

            $table->enum('type', ['rating', 'complaint']);
            $table->decimal('score', 2, 1)->nullable();
            $table->text('message')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('FeedbackType_id')->references('id')->on('feedback_types')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
