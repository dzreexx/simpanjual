@extends('layouts.app')

@php
    $today = \Carbon\Carbon::now();

    $firstMonth = $today->copy()->startOfMonth();
    $lastMonth = $today->copy()->endOfMonth();
@endphp

@section('title', 'Inbound')
@section('content')
    <div class="p-4 space-y-1 bg-white">
        <form
            action="{{ route('dashboard.inboundsetproduct') }}"
            method="POST"
            class="flex space-x-5"
        >
            @csrf
            <select
                class="select appearance-none border-2 border-gray-200 rounded-lg"
                name="warehouse_id"
                onchange="this.form.submit()"
            >
                <option
                    value=""
                    disabled
                    {{ $warehouse_value == '' ? 'selected' : '' }}
                >
                    Warehouse
                </option>
                @foreach ($warehouses as $warehouse)
                    <option
                        value="{{ $warehouse->id_warehouse }}"
                        {{ $warehouse_value == $warehouse->id_warehouse ? 'selected' : '' }}
                    >
                        {{ $warehouse->warehouse_name }}
                    </option>
                @endforeach
            </select>
            <select
                class="select appearance-none border-2 border-gray-200 rounded-lg"
                name="product_id"
                onchange="this.form.submit()"
            >
                <option
                    value=""
                    disabled
                    {{ $product_value == '' ? 'selected' : '' }}
                >
                    Item Code
                </option>
                @foreach ($products as $product)
                    <option
                        value="{{ $product->id_product }}"
                        {{ $product_value == $product->id_product ? 'selected' : '' }}
                    >
                        {{ $product->product_name }}
                    </option>
                @endforeach
            </select>
        </form>

        {{-- Oreder Level --}}

        @if ($selectedProduct)
            <h1>{{ $selectedProduct->product_name }}</h1>
        @endif

        @if ($selectedWarehouse)
            <h1>{{ $selectedWarehouse->warehouse_name }}</h1>
        @endif

        @if ($inventory->isNotEmpty())
            <h1>{{ $inventory->first()->stock }}</h1>
        @endif

        @if ($purchaseOrder)
            @foreach ($purchaseOrder as $po)
                <h1>{{ $po->id_purchase_order }}</h1>
            @endforeach
        @endif

        <div class="w-full my-5 p-5 rounded-lg border-gray-200 border">
            <h1>ORDER LEVEL</h1>
        </div>
        <div class="flex space-x-1 h-[20vh]">
            <div
                class="overflow-x-auto w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
                <div class="w-full h-full">
                    <span
                        class="badge badge-lg badge-warning w-full sticky top-0 bg-white z-10"
                    >
                        Incoming PO
                    </span>

                    @if ($purchaseOrder->where('status', 'pending')->count() > 0)
                        <table class="table table-xs">
                            <thead>
                                <tr>
                                    <th>id_purchase_order</th>
                                    <th>quantity</th>
                                    <th>created_at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseOrder as $po)
                                    @if ($po->status == 'pending')
                                        <tr>
                                            <th>
                                                {{ $po->id_purchase_order }}
                                            </th>
                                            <td>{{ $po->stock }}</td>
                                            <td>{{ $po->created_at }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="flex justify-center items-center h-full">
                            <h2 class="text-3xl font-bold">No data</h2>
                        </div>
                    @endif
                </div>
            </div>

            <div
                class="overflow-x-auto w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
                <div class="w-full h-full">
                    <span
                        class="badge badge-lg badge-warning w-full sticky top-0 bg-white z-10"
                    >
                        PO On Process
                    </span>

                    @if ($purchaseOrder->where('status', 'process')->count() > 0)
                        <table class="table table-xs">
                            <thead>
                                <tr>
                                    <th>id_purchase_order</th>
                                    <th>quantity</th>
                                    <th>created_at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseOrder as $po)
                                    @if ($po->status == 'process')
                                        <tr>
                                            <th>
                                                {{ $po->id_purchase_order }}
                                            </th>
                                            <td>{{ $po->stock }}</td>
                                            <td>{{ $po->created_at }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="flex justify-center items-center h-full">
                            <h2 class="text-3xl font-bold">No data</h2>
                        </div>
                    @endif
                </div>
            </div>

            <div
                class="overflow-x-auto w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
                <div class="w-full h-full">
                    <span
                        class="badge badge-lg badge-warning w-full sticky top-0 bg-white z-10"
                    >
                        PO Accepted at Warehouse
                    </span>

                    @if ($purchaseOrder->where('status', 'done')->count() > 0)
                        <table class="table table-xs">
                            <thead>
                                <tr>
                                    <th>id_purchase_order</th>
                                    <th>quantity</th>
                                    <th>created_at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseOrder as $po)
                                    @if ($po->status == 'done')
                                        <tr>
                                            <th>
                                                {{ $po->id_purchase_order }}
                                            </th>
                                            <td>{{ $po->stock }}</td>
                                            <td>{{ $po->created_at }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="flex justify-center items-center h-full">
                            <h2 class="text-3xl font-bold">No data</h2>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex space-x-1 h-[50vh]">
            <div
                class="card w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
                <div class="w-full h-full">
                    <span
                        class="badge badge-lg badge-warning w-full sticky top-0 bg-white z-10"
                    >
                        Incoming PO
                    </span>

                    @if ($purchaseOrder->where('status', 'pending')->count() > 0)
                        <table class="table table-xs">
                            <thead>
                                <tr>
                                    <th>id_purchase_order</th>
                                    <th>quantity</th>
                                    <th>created_at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseOrder as $po)
                                    @if ($po->status == 'pending')
                                        <tr>
                                            <th>
                                                {{ $po->id_purchase_order }}
                                            </th>
                                            <td>{{ $po->stock }}</td>
                                            <td>{{ $po->created_at }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="flex justify-center items-center h-full">
                            <h2 class="text-3xl font-bold">No data</h2>
                        </div>
                    @endif
                </div>
            </div>
            <div
                class="card w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
                <div class="w-full h-full">
                    <span
                        class="badge badge-lg badge-warning w-full sticky top-0 bg-white z-10"
                    >
                        PO On Process
                    </span>

                    @if ($purchaseOrder->where('status', 'process')->count() > 0)
                        <table class="table table-xs">
                            <thead>
                                <tr>
                                    <th>id_purchase_order</th>
                                    <th>quantity</th>
                                    <th>created_at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseOrder as $po)
                                    @if ($po->status == 'process')
                                        <tr>
                                            <th>
                                                {{ $po->id_purchase_order }}
                                            </th>
                                            <td>{{ $po->stock }}</td>
                                            <td>{{ $po->created_at }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="flex justify-center items-center h-full">
                            <h2 class="text-3xl font-bold">No data</h2>
                        </div>
                    @endif
                </div>
            </div>

            <div
                class="card w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
                <div class="w-full h-full">
                    <span
                        class="badge badge-lg badge-warning w-full sticky top-0 bg-white z-10"
                    >
                        PO Accepted at Warehouse
                    </span>

                    @if ($purchaseOrder->where('status', 'done')->count() > 0)
                        <table class="table table-xs">
                            <thead>
                                <tr>
                                    <th>id_purchase_order</th>
                                    <th>quantity</th>
                                    <th>created_at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseOrder as $po)
                                    @if ($po->status == 'done')
                                        <tr>
                                            <th>
                                                {{ $po->id_purchase_order }}
                                            </th>
                                            <td>{{ $po->stock }}</td>
                                            <td>{{ $po->created_at }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="flex justify-center items-center h-full">
                            <h2 class="text-3xl font-bold">No data</h2>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Item Level --}}

        <div class="w-full my-5 p-5 rounded-lg border-gray-200 border">
            <h1>ITEM LEVEL</h1>
        </div>
        <div class="flex space-x-1 h-[20vh]">
            <div
                class="overflow-x-auto w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
                <div class="w-full h-full">
                    <span
                        class="badge badge-lg badge-warning w-full sticky top-0 bg-white z-10"
                    >
                        Incoming Quantity
                    </span>

                    @if ($purchaseOrder->where('status', 'pending')->count() > 0)
                        <div class="flex justify-center items-center h-full">
                            <h2 class="text-3xl font-bold">
                                {{ $totalPendingStock }}
                            </h2>
                        </div>
                    @else
                        <div class="flex justify-center items-center h-full">
                            <h2 class="text-3xl font-bold">No data</h2>
                        </div>
                    @endif
                </div>
            </div>
            <div
                class="overflow-x-auto w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
                <div class="w-full h-full">
                    <span
                        class="badge badge-lg badge-warning w-full sticky top-0 bg-white z-10"
                    >
                        QTY On Process
                    </span>

                    @if ($purchaseOrder->where('status', 'process')->count() > 0)
                        <div class="flex justify-center items-center h-full">
                            <h2 class="text-3xl font-bold">
                                {{ $totalProcessStock }}
                            </h2>
                        </div>
                    @else
                        <div class="flex justify-center items-center h-full">
                            <h2 class="text-3xl font-bold">No data</h2>
                        </div>
                    @endif
                </div>
            </div>
            <div
                class="overflow-x-auto w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
                <div class="w-full h-full">
                    <span
                        class="badge badge-lg badge-warning w-full sticky top-0 bg-white z-10"
                    >
                        QTY Accepted at Warehouse
                    </span>

                    @if ($purchaseOrder->where('status', 'done')->count() > 0)
                        <div class="flex justify-center items-center h-full">
                            <h2 class="text-3xl font-bold">
                                {{ $totalDoneStock }}
                            </h2>
                        </div>
                    @else
                        <div class="flex justify-center items-center h-full">
                            <h2 class="text-3xl font-bold">No data</h2>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex space-x-1 h-[50vh]">
            <div
                class="card w-full h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
                <div class="w-full h-full">
                    <span
                        class="badge badge-lg badge-warning w-full sticky top-0 bg-white z-10"
                    >
                        Overall Item Qty - Details
                    </span>
                    @if ($purchaseOrder->count() > 0)
                        <table class="table table-xs">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>id_purchase_order</th>
                                    <th>status</th>
                                    <th>quantity</th>
                                    <th>created_at</th>
                                    <th>updated_at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseOrder as $po)
                                    @if ($po)
                                        <tr>
                                            <th>{{ $loop->iteration }}</th>
                                            <th>
                                                {{ $po->id_purchase_order }}
                                            </th>
                                            <td>{{ $po->status }}</td>
                                            <td>{{ $po->stock }}</td>
                                            <td>{{ $po->created_at }}</td>
                                            <td>{{ $po->updated_at }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot class="sticky bottom-0 bg-white">
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>
                                        {{ $po->where('id_warehouse', $warehouse_value)->where('id_product', $product_value)->sum('stock') }}
                                    </th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    @else
                        <div class="flex justify-center items-center h-full">
                            <h2 class="text-3xl font-bold">No data</h2>
                        </div>
                    @endif
                    <!-- <div class="flex justify-center items-center h-full">
                        <h2 class="text-3xl font-bold">No data</h2>
                        {{-- <span class="text-xl">$29/mo</span> --}}
                    </div> -->
                </div>
            </div>
        </div>
        <div class="flex space-x-1 h-[50vh]">
            <div
                class="card w-full h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
                <div class="w-full h-full">
                    <span
                        class="badge badge-lg badge-warning w-full sticky top-0 bg-white z-10"
                    >
                        Stock After Inbound
                    </span>

                    @if ($purchaseOrder->count() > 0 || $inventory->isNotEmpty())
                        <div
                            class="stats stats-vertical lg:stats-horizontal shadow w-full h-full flex justify-center items-center"
                        >
                            <div
                                class="stat flex flex-col justify-center items-center"
                            >
                                <div class="stat-title">Stock Done</div>
                                <div class="stat-value">
                                    <td>
                                        {{ $totalDoneStock }}
                                    </td>
                                </div>
                                <div class="stat-desc">
                                    {{ $firstMonth->format('j F') }} -
                                    {{ $lastMonth->format('j F') }}
                                </div>
                            </div>

                            <div
                                class="stat flex flex-col justify-center items-center"
                            >
                                <div class="stat-title">
                                    Current Stock Warehouse
                                </div>
                                <div class="stat-value">
                                    {{ $totalDoneStock + $inventory->sum('stock') }}
                                </div>
                                <div class="stat-desc">
                                    @php
                                        // Menghitung total stok yang ada di inventory dan stok done
                                        $currentStock = $totalDoneStock + $inventory->sum('stock');

                                        // Menghitung persentase berdasarkan rumus yang benar
                                        $percentage = 0;
                                        if ($currentStock > 0) {
                                            $percentage = ($totalDoneStock / $currentStock) * 100;
                                        }
                                    @endphp

                                    ↗ {{ $totalDoneStock }}
                                    ({{ number_format($percentage, 2) }}%)
                                </div>
                                <!-- <div class="stat-desc">↘︎ 90 (14%)</div> -->
                            </div>
                        </div>
                    @else
                        <div class="flex justify-center items-center h-full">
                            <h2 class="text-3xl font-bold">No data</h2>
                            {{-- <span class="text-xl">$29/mo</span> --}}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
