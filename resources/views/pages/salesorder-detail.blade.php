@extends('layouts.app')

@section('content')
<div class="p-6 h-full w-full bg-slate-50 min-h-screen">
    {{-- Breadcrumb --}}
    <div class="text-sm breadcrumbs text-gray-500 mb-4">
        <ul>
            <li><a href="{{ route('salesorder') }}">Sales Order</a></li>
            <li>Sales Order Detail</li>
        </ul>
    </div>

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h1 class="text-2xl font-bold text-sky-600">{{ $salesOrder->so_number }}</h1>
            @php
                $statusColors = [
                    'unpaid'       => 'badge-warning',
                    'new order'    => 'badge-info',
                    'hold'         => 'badge-ghost',
                    'ready to ship'=> 'badge-primary',
                    'shipping'     => 'badge-accent',
                    'completed'    => 'badge-success',
                    'cancelled'    => 'badge-error',
                    'missing data' => 'badge-ghost',
                    'oversell'     => 'badge-error',
                ];
                $badgeClass = $statusColors[strtolower($salesOrder->status ?? '')] ?? 'badge-ghost';
            @endphp
            <span class="badge {{ $badgeClass }} capitalize">{{ $salesOrder->status }}</span>
        </div>
        <button class="btn btn-outline btn-sm rounded-full px-6 font-normal">Edit</button>
    </div>

    {{-- Tabs --}}
    <div class="tabs mb-6 border-b border-gray-200">
        <button class="tab tab-bordered tab-active text-sky-600 font-medium" id="tab-detail" onclick="switchTab('detail')">Order Detail</button>
        <button class="tab tab-bordered font-medium" id="tab-log" onclick="switchTab('log')">Activity Log</button>
    </div>

    {{-- Tab Content: Order Detail --}}
    <div id="content-detail" class="space-y-6">
        
        {{-- Card 1: Customer Information --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-gray-500"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                Customer Information
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-y-6 gap-x-4 text-sm">
                <div>
                    <p class="text-gray-500 mb-1">Name</p>
                    <p class="font-medium">{{ $salesOrder->so_customer ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Phone</p>
                    <p class="font-medium">-</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Email</p>
                    <p class="font-medium">-</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Address</p>
                    <p class="font-medium">-</p>
                </div>
            </div>
        </div>

        {{-- Card 2: Order Information --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-gray-500"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                Order Information
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-y-6 gap-x-4 text-sm">
                <div>
                    <p class="text-gray-500 mb-1">Brand</p>
                    <p class="font-medium">{{ $salesOrder->product->brand->brand_name ?? 'SimpanJual' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Sales Channel</p>
                    <p class="font-medium">-</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Shipping Service</p>
                    <p class="font-medium">-</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Airway Bill</p>
                    <p class="font-medium">-</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Source Location</p>
                    <p class="font-medium">{{ $salesOrder->warehouse->warehouse_name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Created Date</p>
                    <p class="font-medium">{{ $salesOrder->created_at->format('Y-m-d\TH:i:s.uP') }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Delivery Due Date</p>
                    <p class="font-medium">{{ $salesOrder->delivery_due_date ? \Carbon\Carbon::parse($salesOrder->delivery_due_date)->format('Y-m-d') : '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Related Documents</p>
                    <p class="font-medium">-</p>
                </div>
                <div class="col-span-4">
                    <p class="text-gray-500 mb-1">Note</p>
                    <p class="font-medium">-</p>
                </div>
            </div>
        </div>

        {{-- Card 3: Item List & Payment Detail --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 overflow-hidden">
            <h2 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-gray-500"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                Item List & Payment Detail
            </h2>
            
            <div class="overflow-x-auto mb-6">
                <table class="w-full text-sm text-left">
                    <thead class="text-gray-500 border-b border-gray-100">
                        <tr>
                            <th class="pb-3 font-normal w-1/2">Item</th>
                            <th class="pb-3 font-normal">Quantity</th>
                            <th class="pb-3 font-normal">Selling Price</th>
                            <th class="pb-3 font-normal">Item Discount</th>
                            <th class="pb-3 font-normal text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="py-4">
                                <p class="font-medium text-gray-800">{{ $salesOrder->product->product_code ?? '-' }}</p>
                                <p class="text-gray-500 text-xs">{{ $salesOrder->product->product_name ?? '-' }}</p>
                            </td>
                            <td class="py-4 font-medium">{{ number_format($salesOrder->total_amount) }} pcs</td>
                            <td class="py-4 text-gray-500">
                                @php
                                    $sellingPrice = $salesOrder->total_amount > 0 ? ($salesOrder->grand_total / $salesOrder->total_amount) : 0;
                                @endphp
                                Rp {{ number_format($sellingPrice, 0, ',', '.') }}
                            </td>
                            <td class="py-4 text-gray-500">- Rp 0</td>
                            <td class="py-4 text-right font-medium text-gray-800">Rp {{ number_format($salesOrder->grand_total, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between items-end border-t border-gray-100 pt-6">
                <div class="text-xs text-gray-400">
                    Showing 1-1 of 1 data.
                </div>
                
                <div class="w-80 text-sm space-y-2">
                    <div class="flex justify-between text-gray-500">
                        <span>Payment method</span>
                        <span>-</span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Total Quantity</span>
                        <span>{{ number_format($salesOrder->total_amount) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Sub-total</span>
                        <span>Rp {{ number_format($salesOrder->grand_total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Order Discount</span>
                        <span>Rp 0</span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Shipping price</span>
                        <span>Rp 0</span>
                    </div>
                    <div class="flex justify-between text-gray-500 mb-2">
                        <span>Additional charge</span>
                        <span>Rp 0</span>
                    </div>
                    <div class="flex justify-between font-bold text-gray-800 pt-3 border-t border-gray-100">
                        <span>Grand Total</span>
                        <span>Rp {{ number_format($salesOrder->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab Content: Activity Log --}}
    <div id="content-log" class="hidden space-y-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-800 mb-6">Activity Log</h2>
            
            @if($salesOrder->logs->isEmpty())
                <div class="text-center text-gray-400 py-8">
                    No activity logs found.
                </div>
            @else
                <div class="relative border-l border-gray-200 ml-3 space-y-8">
                    @foreach($salesOrder->logs as $log)
                    <div class="mb-8 ml-6">
                        <span class="absolute flex items-center justify-center w-6 h-6 bg-sky-100 rounded-full -left-3 ring-8 ring-white">
                            <svg class="w-3 h-3 text-sky-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                            </svg>
                        </span>
                        <h3 class="flex items-center mb-1 text-md font-semibold text-gray-900">
                            {{ $log->action }}
                        </h3>
                        <time class="block mb-2 text-sm font-normal leading-none text-gray-400">
                            {{ $log->created_at->format('d M Y, H:i:s') }} 
                            by <span class="font-medium text-gray-600">{{ $log->user->name ?? 'System' }}</span>
                        </time>
                        <p class="text-sm font-normal text-gray-500">{{ $log->description }}</p>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function switchTab(tab) {
        // Tab styling
        document.getElementById('tab-detail').classList.remove('tab-active', 'text-sky-600');
        document.getElementById('tab-log').classList.remove('tab-active', 'text-sky-600');
        
        document.getElementById('tab-' + tab).classList.add('tab-active', 'text-sky-600');

        // Content visibility
        document.getElementById('content-detail').classList.add('hidden');
        document.getElementById('content-log').classList.add('hidden');
        
        document.getElementById('content-' + tab).classList.remove('hidden');
    }
</script>
@endsection
