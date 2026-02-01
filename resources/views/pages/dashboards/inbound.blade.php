@extends('layouts.app')

@section('title', 'Inbound')
@section('content')
    <div class="p-4 space-y-1 bg-white">
            <form action="{{ route('dashboard.inboundsetproduct') }}" method="POST" class="flex space-x-5">
                @csrf
                <select class="select appearance-none border-2 border-gray-200 rounded-lg" name="warehouse_id" onchange="this.form.submit()">
                    <option value="" disabled {{ $warehouse_value == '' ? 'selected' : '' }}>Warehouse</option>
                    @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse->id_warehouse }}" {{ $warehouse_value == $warehouse->id_warehouse ? 'selected' : '' }}>
                            {{ $warehouse->warehouse_name }}
                        </option>
                    @endforeach
                </select>
                <select class="select appearance-none border-2 border-gray-200 rounded-lg" name="product_id"
                    onchange="this.form.submit()">
                    <option value="" disabled {{ $product_value == '' ? 'selected' : '' }}>Item Code</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id_product }}" {{ $product_value == $product->id_product ? 'selected' : '' }}>
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
        @if ($inventory)
            <h1>{{ $inventory->stock }}</h1>
        @endif


        <div class="w-full my-5 p-5 rounded-lg border-gray-200 border">
            <h1>ORDER LEVEL</h1>
        </div>
        <div class="flex space-x-1 h-[20vh]">
            <div class="card w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border">
                <div class="card-body">
                    <span class="badge badge-lg badge-warning">
                        Incoming PO
                    </span>
                    <div class="flex justify-center items-center h-full">
                        <h2 class="text-3xl font-bold">No data</h2>
                        {{-- <span class="text-xl">$29/mo</span> --}}
                    </div>
                </div>
            </div>
            <div class="card w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border">
                <div class="card-body">
                    <span class="badge badge-lg badge-warning">
                        PO On Process
                    </span>
                    <div class="flex justify-center items-center h-full">
                        <h2 class="text-3xl font-bold">No data</h2>
                        {{-- <span class="text-xl">$29/mo</span> --}}
                    </div>
                </div>
            </div>
            <div class="card w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border">
                <div class="card-body">
                    <span class="badge badge-lg badge-warning">
                        PO Accepted at Warehouse
                    </span>
                    <div class="flex justify-center items-center h-full">
                        <h2 class="text-3xl font-bold">No data</h2>
                        {{-- <span class="text-xl">$29/mo</span> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="flex space-x-1 h-[50vh]">
            <div class="card w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border">
                <div class="card-body">
                    <span class="badge badge-lg badge-warning">
                        Incoming PO
                    </span>
                    <div class="flex justify-center items-center h-full">
                        <h2 class="text-3xl font-bold">No data</h2>
                        {{-- <span class="text-xl">$29/mo</span> --}}
                    </div>
                </div>
            </div>
            <div class="card w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border">
                <div class="card-body">
                    <span class="badge badge-lg badge-warning">
                        PO On Process
                    </span>
                    <div class="flex justify-center items-center h-full">
                        <h2 class="text-3xl font-bold">No data</h2>
                        {{-- <span class="text-xl">$29/mo</span> --}}
                    </div>
                </div>
            </div>
            <div class="card w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border">
                <div class="card-body">
                    <span class="badge badge-lg badge-warning">
                        PO Accepted at Warehouse
                    </span>
                    <div class="flex justify-center items-center h-full">
                        <h2 class="text-3xl font-bold">No data</h2>
                        {{-- <span class="text-xl">$29/mo</span> --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- Item Level --}}

        <div class="w-full my-5 p-5 rounded-lg border-gray-200 border">
            <h1>ITEM LEVEL</h1>
        </div>
        <div class="flex space-x-1 h-[20vh]">
            <div class="card w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border">
                <div class="card-body">
                    <span class="badge badge-lg badge-warning">
                        Incoming Quantity
                    </span>
                    <div class="flex justify-center items-center h-full">
                        <h2 class="text-3xl font-bold">No data</h2>
                        {{-- <span class="text-xl">$29/mo</span> --}}
                    </div>
                </div>
            </div>
            <div class="card w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border">
                <div class="card-body">
                    <span class="badge badge-lg badge-warning">
                        QTY On Process
                    </span>
                    <div class="flex justify-center items-center h-full">
                        <h2 class="text-3xl font-bold">No data</h2>
                        {{-- <span class="text-xl">$29/mo</span> --}}
                    </div>
                </div>
            </div>
            <div class="card w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border">
                <div class="card-body">
                    <span class="badge badge-lg badge-warning">
                        QTY Accepted at Warehouse
                    </span>
                    <div class="flex justify-center items-center h-full">
                        <h2 class="text-3xl font-bold">No data</h2>
                        {{-- <span class="text-xl">$29/mo</span> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="flex space-x-1 h-[50vh]">
            <div class="card w-full h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border">
                <div class="card-body">
                    <span class="badge badge-lg badge-warning">
                        Overall Item Qty - Details
                    </span>
                    <div class="flex justify-center items-center h-full">
                        <h2 class="text-3xl font-bold">No data</h2>
                        {{-- <span class="text-xl">$29/mo</span> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="flex space-x-1 h-[50vh]">
            <div class="card w-full h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border">
                <div class="card-body">
                    <span class="badge badge-lg badge-warning">
                        Stock After Inbound
                    </span>
                    <div class="flex justify-center items-center h-full">
                        <h2 class="text-3xl font-bold">No data</h2>
                        {{-- <span class="text-xl">$29/mo</span> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection