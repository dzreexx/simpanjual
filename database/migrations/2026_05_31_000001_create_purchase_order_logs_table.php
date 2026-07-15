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
        Schema::create('purchase_order_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_purchase_order');
            $table->unsignedBigInteger('id_user')->nullable();
            $table->string('action');
            $table->text('description')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_purchase_order')->references('id_purchase_order')->on('purchase_orders')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_logs');
    }
};
