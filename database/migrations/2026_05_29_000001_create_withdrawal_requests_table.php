<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->string('wr_number')->primary();
            $table->enum('status', ['on going', 'done'])->default('on going');
            $table->string('purpose');
            $table->foreignId('source_location')
                ->constrained('warehouses', 'id_warehouse')
                ->onDelete('cascade');
            $table->string('tracking_number')->nullable();
            $table->string('expedition_name')->nullable();
            $table->text('notes')->nullable();
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->string('recipient_email')->nullable();
            $table->text('recipient_address');
            $table->timestamps();
        });

        Schema::create('withdrawal_request_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('wr_number');
            $table->foreign('wr_number')
                ->references('wr_number')
                ->on('withdrawal_requests')
                ->onDelete('cascade');
            $table->foreignId('id_product')
                ->constrained('products', 'id_product')
                ->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->decimal('total_price', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawal_request_items');
        Schema::dropIfExists('withdrawal_requests');
    }
};
