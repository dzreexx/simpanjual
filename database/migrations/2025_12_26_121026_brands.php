<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->bigIncrements('id_brand');
            $table->string('brand_name');
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id_product');
            $table->string('product_name');
            $table->foreignId('id_brand')
                ->constrained('brands', 'id_brand')
                ->onDelete('cascade');
            $table->integer('price');
            $table->timestamps();
        });

        Schema::create('warehouses', function (Blueprint $table) {
            $table->bigIncrements('id_warehouse');
            $table->string('warehouse_name');
            $table->timestamps();
        });

        Schema::create('inventories', function (Blueprint $table) {
            $table->bigIncrements('id_inventory');
            $table->foreignId('id_product')
                ->constrained('products', 'id_product')
                ->onDelete('cascade');
            $table->foreignId('id_warehouse')
                ->constrained('warehouses', 'id_warehouse')
                ->onDelete('cascade');
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('inventories');
    }
};
