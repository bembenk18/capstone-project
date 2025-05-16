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
    Schema::table('products', function (Blueprint $table) {
        $table->unsignedBigInteger('warehouse_id')->after('id');
        $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
        $table->string('image')->nullable()->after('stock');
    });
}

public function down(): void
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropForeign(['warehouse_id']);
        $table->dropColumn(['warehouse_id', 'image']);
    });
}

};
