؟<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePackageExtrasAddFields extends Migration
{
    public function up()
    {
        Schema::table('package_extras', function (Blueprint $table) {
            $table->enum('type', ['food_item', 'service', 'simple'])->after('package_id');
            $table->unsignedBigInteger('food_item_id')->nullable()->after('type');
            $table->unsignedBigInteger('branch_service_type_id')->nullable()->after('food_item_id');
            $table->foreign('food_item_id')->references('id')->on('food_items')->nullOnDelete();
            $table->foreign('branch_service_type_id')->references('id')->on('branch_service_types')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('package_extras', function (Blueprint $table) {
            $table->dropForeign(['food_item_id']);
            $table->dropForeign(['branch_service_type_id']);
            $table->dropColumn(['type', 'food_item_id', 'branch_service_type_id']);
        });
    }
}
