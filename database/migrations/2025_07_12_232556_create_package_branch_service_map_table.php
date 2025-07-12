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
        Schema::create('package_branch_service_map', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('branch_service_type_id');
            $table->timestamps();

            $table->foreign('package_id')
                ->references('id')->on('packages')
                ->onDelete('cascade');

            $table->foreign('branch_service_type_id')
                ->references('id')->on('branch_service_types')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_branch_service_map');
    }
};
