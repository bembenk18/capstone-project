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
        Schema::table('transactions', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->unsignedBigInteger('warehouse_id')->after('product_id');
    
            $table->foreign('warehouse_id')
                  ->references('id')
                  ->on('warehouses')
                  ->onDelete('cascade');
        });
    }
    
    public function down(): void
    {
        Schema::table('transactions', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn('warehouse_id');
        });
    }
    
};
