<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('po_number')->nullable()->unique()->after('id_purchase_order');
            $table->integer('buying_price')->default(0)->after('stock');
            $table->string('vendor_name')->nullable()->after('buying_price');
            $table->string('vendor_phone')->nullable()->after('vendor_name');
            $table->string('vendor_email')->nullable()->after('vendor_phone');
            $table->text('vendor_address')->nullable()->after('vendor_email');
        });

        // Generate po_number for existing records
        $orders = DB::table('purchase_orders')->whereNull('po_number')->get();
        foreach ($orders as $order) {
            DB::table('purchase_orders')
                ->where('id_purchase_order', $order->id_purchase_order)
                ->update([
                    'po_number' => 'PO-' . str_pad($order->id_purchase_order, 5, '0', STR_PAD_LEFT),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['po_number', 'buying_price', 'vendor_name', 'vendor_phone', 'vendor_email', 'vendor_address']);
        });
    }
};
