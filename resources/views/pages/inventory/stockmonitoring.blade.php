@extends('layouts.app')

@section('title', 'Stock Monitoring')

@section('content')
<div class="bg-base-100 p-6 h-full font-sans">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-sky-600 flex items-center gap-2">
            Stock Monitoring
            <span class="tooltip tooltip-right" data-tip="Memantau kuantitas stok barang di gudang">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-sky-400 cursor-pointer">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>
                </svg>
            </span>
        </h1>
    </div>

    {{-- Brand Alert --}}
    @if(!$brandId)
        <div class="alert mb-4 rounded-lg flex items-start gap-2" style="background:#fff7ed;border:1px solid #f97316;color:#9a3412;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <span class="text-sm font-medium">Pilih Brand di sidebar terlebih dahulu.</span>
        </div>
    @endif

    {{-- Filter Warehouse Dropdown --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
        <form action="{{ route('inventory.stockmonitoring') }}" method="GET" class="flex items-center gap-3 w-full md:w-auto">
            <label class="font-semibold text-gray-700 whitespace-nowrap">Warehouse Option:</label>
            <select name="warehouse_id" class="select select-bordered w-full md:w-64 focus:outline-sky-400" onchange="this.form.submit()">
                <option value="" disabled {{ !$warehouseId ? 'selected' : '' }}>Pilih Gudang</option>
                @foreach($warehouses as $wh)
                    <option value="{{ $wh->id_warehouse }}" {{ $warehouseId == $wh->id_warehouse ? 'selected' : '' }}>
                        {{ $wh->warehouse_name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Table --}}
    @if($warehouseId)
    <div class="border rounded-lg bg-white overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm">
                        <th class="font-medium">Product Item</th>
                        <th class="font-medium text-center w-36">Actual Stock</th>
                        <th class="font-medium text-center w-36">Reserved Stock</th>
                        <th class="font-medium text-center w-36">Available Stock</th>
                        <th class="font-medium text-center w-36">Pre-order Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100">
                            <td class="font-semibold text-gray-700">{{ $product->product_name }}</td>
                            <td class="text-center">
                                <span class="badge badge-lg bg-sky-100 text-sky-700 border-none font-bold">
                                    {{ $product->actual_stock }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-lg bg-red-100 text-red-600 border-none font-bold">
                                    {{ $product->reserve_stock }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-lg bg-green-100 text-green-700 border-none font-bold">
                                    {{ $product->available_stock }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-lg bg-orange-100 text-orange-600 border-none font-bold">
                                    {{ $product->preorder_stock }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5" stroke="currentColor" class="w-24 h-24 mb-4 text-gray-200">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>
                                    <p class="text-lg font-medium text-gray-500">Belum ada produk untuk brand ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($products->total() > 0)
    <div class="flex justify-between items-center mt-4 pb-4 text-xs text-gray-500">
        <div>Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} entries</div>
        <div class="flex items-center gap-2">
            @if($products->onFirstPage())
                <button class="btn btn-sm btn-ghost text-gray-300" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                </button>
            @else
                <a href="{{ $products->appends(request()->query())->previousPageUrl() }}" class="btn btn-sm btn-ghost text-sky-500 hover:bg-sky-50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                </a>
            @endif

            @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                @if($page == $products->currentPage())
                    <button class="btn btn-sm bg-sky-500 text-white border-none hover:bg-sky-600">{{ $page }}</button>
                @else
                    <a href="{{ $url . '&' . http_build_query(request()->except('page')) }}" class="btn btn-sm btn-outline border-gray-200 text-gray-500 hover:bg-gray-50">{{ $page }}</a>
                @endif
            @endforeach

            @if($products->hasMorePages())
                <a href="{{ $products->appends(request()->query())->nextPageUrl() }}" class="btn btn-sm btn-ghost text-sky-500 hover:bg-sky-50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </a>
            @else
                <button class="btn btn-sm btn-ghost text-gray-300" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </button>
            @endif
        </div>
    </div>
    @endif

    @else
    <div class="flex flex-col items-center justify-center py-20 text-gray-400 border rounded-lg bg-white shadow-sm mt-4">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5" stroke="currentColor" class="w-24 h-24 mb-4 text-gray-300">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2.25V15m0 0l-3-3m3 3l3-3m-9-6h12a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 19.5v-9A2.25 2.25 0 013.75 6z" />
        </svg>
        <p class="text-lg font-medium text-gray-500">Pilih Option Gudang untuk melihat Stock Monitoring.</p>
    </div>
    @endif
</div>
@endsection
