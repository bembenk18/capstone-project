<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop foreign key dulu
            $table->dropForeign(['warehouse_id']);

            // Baru drop kolom
            $table->dropColumn('warehouse_id');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_id')->nullable();

            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('set null');
        });
    }
};

