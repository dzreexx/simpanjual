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
        Schema::create('sales_order_logs', function (Blueprint $table) {
            $table->id();
            $table->string('so_number');
            $table->unsignedBigInteger('id_user')->nullable(); // nullable untuk sistem/import auto
            $table->string('action');
            $table->text('description')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('so_number')->references('so_number')->on('sales_order')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_order_logs');
    }
};
