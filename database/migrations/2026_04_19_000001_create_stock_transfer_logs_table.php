<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfer_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tr_number');
            $table->foreign('tr_number')
                ->references('tr_number')
                ->on('stock_transfers')
                ->onDelete('cascade');
            $table->unsignedBigInteger('id_user')->nullable();
            $table->foreign('id_user')
                ->references('id_user')
                ->on('users')
                ->onDelete('set null');
            // 'transfer' = overall transfer status change, 'item' = item-level change
            $table->enum('type', ['transfer', 'item'])->default('transfer');
            $table->string('old_status')->nullable();
            $table->string('new_status');
            // for item-level logs
            $table->unsignedBigInteger('item_id')->nullable();
            $table->foreign('item_id')
                ->references('id')
                ->on('stock_transfer_items')
                ->onDelete('set null');
            $table->string('item_name')->nullable(); // snapshot product name at time of log
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_logs');
    }
};
