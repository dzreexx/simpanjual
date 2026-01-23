@extends('layouts.app')

@section('title', 'Inbound')
@section('content')
    <div class="p-4 space-y-1 bg-white">
        <div class="flex space-x-5">
            <select
                class="select appearance-none border-2 border-gray-200 rounded-lg"
            >
                <option disabled selected>Warehouse</option>
                <option>Crimson</option>
                <option>Amber</option>
                <option>Velvet</option>
            </select>
            <select
                class="select appearance-none border-2 border-gray-200 rounded-lg"
            >
                <option disabled selected>Item Code</option>
                <option>Crimson</option>
                <option>Amber</option>
                <option>Velvet</option>
            </select>
        </div>

        {{-- Oreder Level --}}

        <div class="w-full my-5 p-5 rounded-lg border-gray-200 border">
            <h1>ORDER LEVEL</h1>
        </div>
        <div class="flex space-x-1 h-[20vh]">
            <div
                class="card w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
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
            <div
                class="card w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
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
            <div
                class="card w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
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
            <div
                class="card w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
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
            <div
                class="card w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
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
            <div
                class="card w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
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
            <div
                class="card w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
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
            <div
                class="card w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
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
            <div
                class="card w-1/3 h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
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
            <div
                class="card w-full h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
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
            <div
                class="card w-full h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
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
        <p>{{ $brand_id }}</p>
        @foreach ($product_name as $product)
            <p>{{ $product }}</p>
        @endforeach
    </div>
@endsection
