<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->string('tr_number')->primary();
            $table->enum('status', [
                'picking',
                'packing',
                'on delivery',
                'receiving',
                'submit qc',
                'received',
                'accepted at warehouse',
            ])->default('picking');
            $table->foreignId('source_location')
                ->constrained('warehouses', 'id_warehouse')
                ->onDelete('cascade');
            $table->foreignId('destination_location')
                ->constrained('warehouses', 'id_warehouse')
                ->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tr_number');
            $table->foreign('tr_number')
                ->references('tr_number')
                ->on('stock_transfers')
                ->onDelete('cascade');
            $table->foreignId('id_product')
                ->constrained('products', 'id_product')
                ->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->integer('delivery_quantity')->default(0);
            $table->integer('received_qty')->default(0);
            $table->enum('item_status', [
                'picking',
                'packing',
                'on delivery',
                'receiving',
                'submit qc',
                'received',
                'accepted at warehouse',
            ])->default('picking');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
    }
};
