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
        Schema::create('sales_order', function (Blueprint $table) {
            $table->string('so_number')->primary()->unique();
            $table->string('so_customer');
            $table->enum('status', [
            'unpaid', 
            'new order', 
            'hold', 
            'ready to ship', 
            'shipping', 
            'completed', 
            'cancelled', 
            'missing data', 
            'oversell'
            ])->default('unpaid');
            $table->integer('total_amount');
            $table->integer('grand_total');
            $table->date('delivery_due_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->foreignId('id_brand')
                ->constrained('brands', 'id_brand')
                ->onDelete('cascade');
            $table->foreignId('id_product')
                ->constrained('products', 'id_product')
                ->onDelete('cascade');
            $table->foreignId('source_location')
                ->constrained('warehouses', 'id_warehouse')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_order');
    }
};
