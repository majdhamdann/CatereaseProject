<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('packages', function (Blueprint $table) {
        $table->integer('max_extra_persons')->default(0)->after('serves_count');
        $table->decimal('price_per_extra_person', 10, 2)->default(0.00)->after('max_extra_persons');
    });

    DB::statement('ALTER TABLE packages CHANGE service_type_id branch_service_type_id BIGINT UNSIGNED NULL');
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('packages', function (Blueprint $table) {
            $table->renameColumn('branch_service_type_id', 'service_type_id');
            $table->dropColumn(['max_extra_persons', 'price_per_extra_person']);
        });
    }
};
