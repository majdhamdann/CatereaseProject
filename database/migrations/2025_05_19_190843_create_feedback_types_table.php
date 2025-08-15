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
        Schema::create('feedback_types', function (Blueprint $table) {
            $table->id();
            $table->enum('target_type', ['restaurant', 'branch','package', 'delivery_person', 'service']);
            $table->unsignedBigInteger('target_ref_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_types');
    }
};
