@extends('layouts.app')

@section('title', 'Stock Transfer')

@section('content')
<div class="bg-base-100 p-6 h-full font-sans" x-data="{ openSearch: false, search: '{{ request('search') }}' }">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-sky-600 flex items-center gap-2">
            Stock Transfer
            <span class="tooltip tooltip-right" data-tip="Perpindahan stok antar gudang">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-sky-400 cursor-pointer">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>
                </svg>
            </span>
        </h1>
        <div class="flex gap-3">
            <button onclick="filter_modal.showModal()" class="btn btn-sm btn-outline border-gray-300 font-normal normal-case">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.5a1 1 0 01-.293.707L13 14.414V19a1 1 0 01-.447.894l-4 2.667A1 1 0 017 21.667V14.414L3.293 7.207A1 1 0 013 6.5V4z"/>
                </svg>
                All Filter
                @if(request()->anyFilled(['start_date','end_date']))
                    <span class="badge badge-info badge-xs ml-1">!</span>
                @endif
            </button>
            <button onclick="create_modal.showModal()" class="btn btn-sm bg-sky-500 hover:bg-sky-600 text-white border-none gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Create New
            </button>
        </div>
    </div>

    {{-- Flash Alerts --}}
    @if(session('success'))
        <div class="alert alert-success mb-4 rounded-lg flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert mb-4 rounded-lg flex items-start gap-2" style="background:#fff7ed;border:1px solid #f97316;color:#9a3412;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <span class="text-sm font-medium">{{ session('warning') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-error mb-4 rounded-lg">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Status Tabs --}}
    @php
        $tabs = array_merge(['all' => 'All'], $statuses);
    @endphp
    <div class="mb-6 overflow-x-auto">
        <div class="flex space-x-6 text-sm text-gray-500 border-b border-gray-200 pb-2 min-w-max">
            @foreach($tabs as $slug => $label)
                <a href="{{ route('stocktransfer', array_merge(['status' => $slug === 'all' ? null : $slug], request()->except('status'))) }}"
                   class="px-1 pb-2 whitespace-nowrap {{ $currentStatus == $slug ? 'text-sky-600 font-semibold border-b-2 border-sky-600' : 'hover:text-gray-700' }}">
                    {{ $label }}
                    @if($slug !== 'all')
                        <span class="ml-1 text-xs text-gray-400">({{ $stockTransfers->total() > 0 && $currentStatus == $slug ? $stockTransfers->total() : '' }})</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
        <form action="{{ url()->current() }}" method="GET" id="perPageForm" class="flex items-center gap-3">
            <select class="select select-sm appearance-none border-2 border-gray-200 rounded-lg" name="per_page" onchange="this.form.submit()">
                @foreach([5,10,25,50] as $n)
                    <option value="{{ $n }}" {{ request('per_page', 10) == $n ? 'selected' : '' }}>{{ $n }} Rows</option>
                @endforeach
            </select>
        </form>

        <div class="flex items-center gap-3 w-full md:w-auto">
            {{-- Search --}}
            <div class="relative w-full md:w-80">
                <form action="{{ url()->current() }}" method="GET" id="searchForm">
                    @foreach(request()->except(['search','search_by']) as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}"/>
                    @endforeach
                    <div class="relative flex items-center">
                        <input type="text" name="search" x-model="search"
                               @input="openSearch = search.length > 0"
                               @focus="if(search.length > 0) openSearch = true"
                               @click.away="openSearch = false"
                               placeholder="Cari TR Number, produk, gudang..."
                               class="input input-sm input-bordered w-full pr-16 rounded-full focus:outline-sky-500"
                               autocomplete="off"/>
                        <input type="hidden" name="search_by" id="searchBy"/>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-1">
                            <template x-if="search.length > 0">
                                <button type="button" @click="window.location.href='{{ url()->current() }}'" class="p-1 text-gray-400 hover:text-red-500">✕</button>
                            </template>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/>
                            </svg>
                        </div>
                    </div>
                    <div x-show="openSearch" class="absolute z-50 w-full mt-2 bg-white border rounded-xl shadow-xl">
                        <button type="submit" @click="document.getElementById('searchBy').value='tr_number'" class="w-full px-4 py-2 text-sm text-left hover:bg-sky-50">TR Number: <span x-text="search" class="font-bold"></span></button>
                        <button type="submit" @click="document.getElementById('searchBy').value='product'" class="w-full px-4 py-2 text-sm text-left hover:bg-sky-50">Product: <span x-text="search" class="font-bold"></span></button>
                        <button type="submit" @click="document.getElementById('searchBy').value='warehouse'" class="w-full px-4 py-2 text-sm text-left hover:bg-sky-50">Warehouse: <span x-text="search" class="font-bold"></span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="border rounded-lg bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm">
                        <th class="font-medium">TR Number</th>
                        <th class="font-medium">Status</th>
                        <th class="font-medium">Source Location</th>
                        <th class="font-medium">Destination Location</th>
                        <th class="font-medium">Items</th>
                        <th class="font-medium">Created Date</th>
                        <th class="font-medium">Notes</th>
                        <th class="font-medium text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockTransfers as $tr)
                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100">
                            <td>
                                <button onclick="openDetailModal('{{ $tr->tr_number }}')"
                                        class="text-sky-600 font-semibold hover:underline">
                                    {{ $tr->tr_number }}
                                </button>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'picking'               => 'badge-warning',
                                        'packing'               => 'badge-info',
                                        'on delivery'           => 'badge-primary',
                                        'receiving'             => 'badge-secondary',
                                        'submit qc'             => 'badge-accent',
                                        'received'              => 'badge-success',
                                        'accepted at warehouse' => 'badge-success',
                                    ];
                                    $badgeClass = $statusColors[$tr->status] ?? 'badge-ghost';
                                @endphp
                                <span class="badge {{ $badgeClass }} badge-sm capitalize whitespace-nowrap">
                                    {{ $statuses[$tr->status] ?? $tr->status }}
                                </span>
                            </td>
                            <td class="text-sm text-gray-700">{{ $tr->sourceWarehouse->warehouse_name ?? '-' }}</td>
                            <td class="text-sm text-gray-700">{{ $tr->destinationWarehouse->warehouse_name ?? '-' }}</td>
                            <td class="text-sm text-gray-500">
                                <span class="badge badge-ghost badge-sm">{{ $tr->items->count() }} item(s)</span>
                            </td>
                            <td class="text-xs text-gray-500">{{ $tr->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-xs text-gray-500 max-w-[150px] truncate">{{ $tr->notes ?: '-' }}</td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="openDetailModal('{{ $tr->tr_number }}')" class="btn btn-xs btn-outline btn-info" title="Detail">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </button>
                                    <form action="{{ route('stocktransfer.destroy', $tr->tr_number) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus {{ $tr->tr_number }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-outline btn-error" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5" stroke="currentColor" class="w-24 h-24 mb-4 text-gray-200">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-500">Belum ada Stock Transfer</p>
                                    <p class="text-sm mt-1">Klik <span class="text-sky-500 font-medium">Create New</span> untuk membuat transfer pertama.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($stockTransfers->total() > 0)
    <div class="flex justify-between items-center mt-4 pb-4 text-xs text-gray-500">
        <div>Showing {{ $stockTransfers->firstItem() ?? 0 }} to {{ $stockTransfers->lastItem() ?? 0 }} of {{ $stockTransfers->total() }} entries</div>
        <div class="flex items-center gap-2">
            @if($stockTransfers->onFirstPage())
                <button class="btn btn-sm btn-ghost text-gray-300" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                </button>
            @else
                <a href="{{ $stockTransfers->appends(request()->query())->previousPageUrl() }}" class="btn btn-sm btn-ghost text-sky-500 hover:bg-sky-50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                </a>
            @endif
            @foreach($stockTransfers->getUrlRange(1, $stockTransfers->lastPage()) as $page => $url)
                @if($page == $stockTransfers->currentPage())
                    <button class="btn btn-sm bg-sky-500 text-white border-none hover:bg-sky-600">{{ $page }}</button>
                @else
                    <a href="{{ $url . '&' . http_build_query(request()->except('page')) }}" class="btn btn-sm btn-outline border-gray-200 text-gray-500 hover:bg-gray-50">{{ $page }}</a>
                @endif
            @endforeach
            @if($stockTransfers->hasMorePages())
                <a href="{{ $stockTransfers->appends(request()->query())->nextPageUrl() }}" class="btn btn-sm btn-ghost text-sky-500 hover:bg-sky-50">
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
</div>

{{-- ===================== MODAL CREATE ===================== --}}
<dialog id="create_modal" class="modal">
    <div class="modal-box w-11/12 max-w-3xl bg-white p-0">
        <div class="p-6 border-b flex justify-between items-center">
            <h3 class="font-bold text-xl text-gray-700 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-sky-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                </svg>
                Buat Stock Transfer Baru
            </h3>
            <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost">✕</button></form>
        </div>

        <form action="{{ route('stocktransfer.store') }}" method="POST" id="createForm">
            @csrf
            <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">

                {{-- Warehouse Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label pb-1"><span class="label-text font-semibold text-gray-700">Source Location <span class="text-red-500">*</span></span></label>
                        <select name="source_location" id="source_location" class="select select-bordered w-full focus:outline-sky-400" required onchange="filterDestination()">
                            <option value="" disabled selected>Pilih Gudang Asal</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id_warehouse }}" {{ old('source_location') == $wh->id_warehouse ? 'selected' : '' }}>{{ $wh->warehouse_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label pb-1"><span class="label-text font-semibold text-gray-700">Destination Location <span class="text-red-500">*</span></span></label>
                        <select name="destination_location" id="destination_location" class="select select-bordered w-full focus:outline-sky-400" required>
                            <option value="" disabled selected>Pilih Gudang Tujuan</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id_warehouse }}" {{ old('destination_location') == $wh->id_warehouse ? 'selected' : '' }}>{{ $wh->warehouse_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Item List --}}
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="label-text font-semibold text-gray-700">Item List <span class="text-red-500">*</span></label>
                        <button type="button" onclick="addItemRow()" class="btn btn-xs btn-outline text-sky-600 border-sky-300 hover:bg-sky-50">
                            + Tambah Item
                        </button>
                    </div>
                    <div class="border rounded-lg overflow-hidden">
                        <table class="table table-sm w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-gray-500 font-medium">Product</th>
                                    <th class="text-gray-500 font-medium w-32">Quantity</th>
                                    <th class="w-10"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <tr id="item-row-0">
                                    <td>
                                        <select name="items[0][id_product]" class="select select-bordered select-sm w-full" required>
                                            <option value="" disabled selected>Pilih Produk</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id_product }}">{{ $p->product_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][quantity]" min="1" class="input input-bordered input-sm w-full" placeholder="Qty" required/>
                                        <div id="stock-warning-0" class="mt-1"></div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" onclick="removeRow(0)" class="btn btn-xs btn-ghost text-red-400 hover:text-red-600">✕</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="form-control">
                    <label class="label pb-1"><span class="label-text font-semibold text-gray-700">Notes</span></label>
                    <textarea name="notes" rows="3" class="textarea textarea-bordered w-full focus:outline-sky-400" placeholder="Catatan tambahan (opsional)...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="p-6 border-t flex justify-end gap-3">
                <form method="dialog"><button type="submit" class="btn btn-sm btn-ghost text-gray-500">Batal</button></form>
                <button type="submit" form="createForm" class="btn btn-sm bg-sky-500 hover:bg-sky-600 text-white border-none px-6">
                    Buat Transfer
                </button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>

{{-- ===================== MODAL DETAIL ===================== --}}
<dialog id="detail_modal" class="modal">
    <div class="modal-box w-11/12 max-w-5xl bg-white p-0">
        <div class="p-6 border-b flex justify-between items-center">
            <h3 class="font-bold text-xl text-gray-700 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-sky-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V19.5a2.25 2.25 0 002.25 2.25h.75"/>
                </svg>
                Detail Stock Transfer
            </h3>
            <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost">✕</button></form>
        </div>
        <div id="detail_modal_body" class="p-6 max-h-[80vh] overflow-y-auto">
            <div class="flex justify-center py-8">
                <span class="loading loading-spinner loading-lg text-sky-500"></span>
            </div>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>

{{-- ===================== MODAL FILTER ===================== --}}
<dialog id="filter_modal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl bg-white p-0">
        <div class="p-6 border-b flex justify-between items-center">
            <h3 class="font-bold text-2xl text-gray-700">Filter</h3>
            <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost">✕</button></form>
        </div>
        <form action="{{ route('stocktransfer', ['status' => $status ?? 'all']) }}" method="GET">
            <input type="hidden" name="search" value="{{ request('search') }}"/>
            <input type="hidden" name="per_page" value="{{ request('per_page') }}"/>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label font-bold text-gray-700">Created Date From</label>
                        <input type="date" name="start_date" class="input input-bordered w-full" value="{{ request('start_date') }}"/>
                    </div>
                    <div class="form-control">
                        <label class="label font-bold text-gray-700">Created Date To</label>
                        <input type="date" name="end_date" class="input input-bordered w-full" value="{{ request('end_date') }}"/>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t flex justify-end items-center gap-4">
                <a href="{{ route('stocktransfer', ['status' => $status ?? 'all']) }}" class="text-sky-500 font-semibold">Reset Filter</a>
                <button type="submit" class="btn bg-sky-500 hover:bg-sky-600 text-white border-none rounded-full px-10">Apply Filter</button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>

{{-- ===================== ALERT MODAL ===================== --}}
<dialog id="alert_modal" class="modal">
    <div class="modal-box max-w-md">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-orange-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-800 mb-1">⚠️ Tidak Dapat Diproses</h3>
                <p id="alert_modal_message" class="text-sm text-gray-600"></p>
            </div>
        </div>
        <div class="modal-action mt-4">
            <form method="dialog">
                <button class="btn btn-sm bg-sky-500 hover:bg-sky-600 text-white border-none px-6">Mengerti</button>
            </form>
        </div>
    </div>
</dialog>

<script>
// ────────────────────────────────────────────────────────────
// CONSTANTS
// ────────────────────────────────────────────────────────────
const STATUS_ORDER = ['picking','packing','on delivery','receiving','submit qc','accepted at warehouse'];

const statusLabels = {
    'picking':               'Picking',
    'packing':               'Packing',
    'on delivery':           'On Delivery',
    'receiving':             'Receiving',
    'submit qc':             'Submit QC',
    'accepted at warehouse': 'Accepted at Warehouse',
};

const statusColors = {
    'picking':               'badge-warning',
    'packing':               'badge-info',
    'on delivery':           'badge-primary',
    'receiving':             'badge-secondary',
    'submit qc':             'badge-accent',
    'accepted at warehouse': 'badge-success',
};

// ────────────────────────────────────────────────────────────
// CREATE MODAL — STOCK CHECK
// ────────────────────────────────────────────────────────────
let rowCount = 1;
const products = @json($products->map(fn($p) => ['id' => $p->id_product, 'name' => $p->product_name]));
const stockCache = {};

function getSourceWarehouseId() {
    return document.getElementById('source_location').value;
}

async function fetchStock(productId, warehouseId) {
    if (!productId || !warehouseId) return null;
    const key = `${productId}_${warehouseId}`;
    if (stockCache[key] !== undefined) return stockCache[key];
    try {
        const r = await fetch(`/stocktransfer/check-stock?id_product=${productId}&id_warehouse=${warehouseId}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const d = await r.json();
        stockCache[key] = d.stock;
        return d.stock;
    } catch { return null; }
}

async function checkRowStock(idx) {
    const productSel = document.querySelector(`select[name="items[${idx}][id_product]"]`);
    const qtyInput   = document.querySelector(`input[name="items[${idx}][quantity]"]`);
    const warnDiv    = document.getElementById(`stock-warning-${idx}`);
    if (!productSel || !qtyInput || !warnDiv) return;

    const productId  = productSel.value;
    const qty        = parseInt(qtyInput.value) || 0;
    const warehouseId = getSourceWarehouseId();

    if (!productId || !warehouseId || !qty) { warnDiv.innerHTML = ''; return; }

    // clear cache so we get fresh data
    delete stockCache[`${productId}_${warehouseId}`];
    const stock = await fetchStock(productId, warehouseId);
    if (stock === null) { warnDiv.innerHTML = ''; return; }

    if (stock < qty) {
        warnDiv.innerHTML = `<span class="flex items-center gap-1 text-orange-600 text-xs font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            Stok gudang: <b>${stock}</b> — kurang dari request ${qty}
        </span>`;
    } else {
        warnDiv.innerHTML = `<span class="text-green-600 text-xs">✓ Stok tersedia: ${stock}</span>`;
    }
}

async function checkAllRows() {
    Object.keys(stockCache).forEach(k => delete stockCache[k]);
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const m = row.id.match(/item-row-(\d+)/);
        if (m) checkRowStock(parseInt(m[1]));
    });
}

function setupRowListeners(idx) {
    const sel = document.querySelector(`select[name="items[${idx}][id_product]"]`);
    const inp = document.querySelector(`input[name="items[${idx}][quantity]"]`);
    if (sel) sel.addEventListener('change', () => checkRowStock(idx));
    if (inp) inp.addEventListener('input',  () => checkRowStock(idx));
}

function addItemRow() {
    const idx  = rowCount++;
    const opts = products.map(p => `<option value="${p.id}">${p.name}</option>`).join('');
    const row  = `
    <tr id="item-row-${idx}">
        <td>
            <select name="items[${idx}][id_product]" class="select select-bordered select-sm w-full" required>
                <option value="" disabled selected>Pilih Produk</option>
                ${opts}
            </select>
        </td>
        <td>
            <input type="number" name="items[${idx}][quantity]" min="1"
                   class="input input-bordered input-sm w-full" placeholder="Qty" required/>
            <div id="stock-warning-${idx}" class="mt-1"></div>
        </td>
        <td class="text-center">
            <button type="button" onclick="removeRow(${idx})" class="btn btn-xs btn-ghost text-red-400 hover:text-red-600">✕</button>
        </td>
    </tr>`;
    document.getElementById('itemsTableBody').insertAdjacentHTML('beforeend', row);
    setupRowListeners(idx);
}

function removeRow(idx) {
    const row = document.getElementById(`item-row-${idx}`);
    if (row && document.getElementById('itemsTableBody').children.length > 1) row.remove();
}

function filterDestination() {
    const srcVal  = document.getElementById('source_location').value;
    const destSel = document.getElementById('destination_location');
    Array.from(destSel.options).forEach(opt => {
        opt.disabled = opt.value === srcVal && opt.value !== '';
    });
    if (destSel.value === srcVal) destSel.value = '';
    checkAllRows();
}

document.addEventListener('DOMContentLoaded', () => {
    setupRowListeners(0);
    const srcSel = document.getElementById('source_location');
    if (srcSel) srcSel.addEventListener('change', checkAllRows);
});

// ────────────────────────────────────────────────────────────
// TOAST NOTIFICATION
// ────────────────────────────────────────────────────────────
function showToast(message, type = 'success') {
    const colors = { success: '#22c55e', warning: '#f97316', error: '#ef4444' };
    const icons  = {
        success: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>',
        warning: '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>',
        error:   '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>',
    };
    const t = document.createElement('div');
    t.style.cssText = `position:fixed;bottom:20px;right:20px;z-index:9999;display:flex;align-items:center;gap:10px;padding:12px 18px;border-radius:12px;color:#fff;font-size:14px;font-weight:500;box-shadow:0 8px 24px rgba(0,0,0,.15);background:${colors[type] || colors.success};max-width:380px;`;
    t.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;flex-shrink:0" viewBox="0 0 20 20" fill="currentColor">${icons[type] || icons.success}</svg><span>${message}</span>`;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 4500);
}

function showAlertModal(message) {
    document.getElementById('alert_modal_message').textContent = message;
    document.getElementById('alert_modal').showModal();
}

// ────────────────────────────────────────────────────────────
// DETAIL MODAL
// ────────────────────────────────────────────────────────────
let currentTrNumber = null;

// Forward-only options: only show current + future statuses
function buildStatusOptions(currentVal) {
    const currentIdx = STATUS_ORDER.indexOf(currentVal);
    return STATUS_ORDER
        .filter((_, idx) => idx >= currentIdx)
        .map(val => `<option value="${val}" ${currentVal === val ? 'selected' : ''}>${statusLabels[val]}</option>`)
        .join('');
}

function openDetailModal(trNumber) {
    currentTrNumber = trNumber;
    const modal = document.getElementById('detail_modal');
    const body  = document.getElementById('detail_modal_body');
    body.innerHTML = '<div class="flex justify-center py-8"><span class="loading loading-spinner loading-lg text-sky-500"></span></div>';
    modal.showModal();
    fetchAndRenderDetail(trNumber, body);
}

function reloadDetailModal() {
    if (!currentTrNumber) return;
    const body = document.getElementById('detail_modal_body');
    body.innerHTML = '<div class="flex justify-center py-4"><span class="loading loading-spinner loading-md text-sky-500"></span></div>';
    fetchAndRenderDetail(currentTrNumber, body);
}

function fetchAndRenderDetail(trNumber, body) {
    fetch(`/stocktransfer/detail/${trNumber}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => { body.innerHTML = buildDetailHTML(data); })
    .catch(err => {
        console.error(err);
        body.innerHTML = '<p class="text-red-500 text-center py-8">Gagal memuat data. Coba lagi.</p>';
    });
}

// ── AJAX: Update item (stay in modal) ──────────────────────
async function submitItemUpdate(event, formEl, itemId) {
    event.preventDefault();
    const btn = formEl.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; btn.textContent = '...'; }
    try {
        const r    = await fetch(formEl.action, {
            method: 'POST',
            body:   new FormData(formEl),
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await r.json();
        if (data.error)   showToast(data.message, 'error');
        else if (data.warning) showToast(data.message, 'warning');
        else               showToast(data.message || 'Item berhasil diupdate.', 'success');
    } catch {
        showToast('Gagal menyimpan. Coba lagi.', 'error');
    } finally {
        reloadDetailModal();
    }
}

// ── AJAX: Update transfer status ────────────────────────────
async function submitTransferStatus(event, formEl) {
    event.preventDefault();
    const btn = formEl.querySelector('button[type="submit"]');
    if (btn) btn.disabled = true;
    try {
        const r    = await fetch(formEl.action, {
            method: 'POST',
            body:   new FormData(formEl),
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await r.json();
        if (data.error) {
            showAlertModal(data.message);
            if (btn) btn.disabled = false;
        } else if (data.warning) {
            showToast(data.message, 'warning');
            reloadDetailModal();
        } else {
            showToast(data.message || 'Status berhasil diupdate.', 'success');
            reloadDetailModal();
        }
    } catch {
        showToast('Gagal update status. Coba lagi.', 'error');
        if (btn) btn.disabled = false;
    }
}

// ────────────────────────────────────────────────────────────
// BUILD DETAIL HTML
// ────────────────────────────────────────────────────────────
function buildDetailHTML(data) {
    const tr             = data.transfer;
    const badgeCls       = statusColors[tr.status] || 'badge-ghost';
    const isLocked       = tr.status === 'accepted at warehouse';
    const statusOpts     = buildStatusOptions(tr.status);
    const createdAt      = new Date(tr.created_at).toLocaleString('id-ID', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' });
    const sourceName     = tr.source_warehouse  ? tr.source_warehouse.warehouse_name  : '-';
    const destName       = tr.destination_warehouse ? tr.destination_warehouse.warehouse_name : '-';

    const statusPanel = isLocked
        ? `<div class="flex items-center gap-3 p-4 border border-green-200 rounded-xl bg-green-50">
               <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-500 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
               <span class="text-sm font-semibold text-green-700">Transfer selesai. Stok sudah dipindahkan ke gudang tujuan.</span>
           </div>`
        : `<div class="flex items-center gap-3 p-4 border rounded-xl bg-sky-50">
               <span class="text-sm font-semibold text-gray-700 shrink-0">Update Status Transfer:</span>
               <form method="POST" action="/stocktransfer/${tr.tr_number}/status"
                     onsubmit="submitTransferStatus(event,this)" class="flex items-center gap-2 flex-1">
                   <input type="hidden" name="_token" value="{{ csrf_token() }}">
                   <input type="hidden" name="_method" value="PATCH">
                   <select name="status" class="select select-sm select-bordered flex-1 max-w-xs">${statusOpts}</select>
                   <button type="submit" class="btn btn-sm bg-sky-500 hover:bg-sky-600 text-white border-none">Update</button>
               </form>
           </div>`;

    return `<div class="space-y-5">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-gray-50 rounded-xl p-4"><p class="text-xs text-gray-500 mb-1">TR Number</p><p class="font-bold text-sky-600 text-sm">${tr.tr_number}</p></div>
            <div class="bg-gray-50 rounded-xl p-4"><p class="text-xs text-gray-500 mb-1">Status</p><span class="badge ${badgeCls} badge-sm capitalize">${statusLabels[tr.status] || tr.status}</span></div>
            <div class="bg-gray-50 rounded-xl p-4"><p class="text-xs text-gray-500 mb-1">Source</p><p class="font-semibold text-gray-700 text-sm">${sourceName}</p></div>
            <div class="bg-gray-50 rounded-xl p-4"><p class="text-xs text-gray-500 mb-1">Destination</p><p class="font-semibold text-gray-700 text-sm">${destName}</p></div>
        </div>
        ${statusPanel}
        <div>
            <h4 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg> Item List</h4>
            <div class="border rounded-xl overflow-hidden overflow-x-auto">
                <table class="table table-sm w-full min-w-[660px]">
                    <thead class="bg-gray-50"><tr>
                        <th class="text-gray-500 font-medium">Nama Item</th>
                        <th class="text-gray-500 font-medium text-center w-14">Qty</th>
                        <th class="text-gray-500 font-medium" colspan="3">Update Item</th>
                    </tr></thead>
                    <tbody>${buildItemRows(tr)}</tbody>
                </table>
            </div>
        </div>
        ${tr.notes ? `<div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl"><p class="text-xs text-yellow-700 font-semibold mb-1">Notes</p><p class="text-sm text-gray-700">${tr.notes}</p></div>` : ''}
        <div>
            <h4 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Activity Log</h4>
            <div class="border rounded-xl p-4 bg-gray-50/50">${buildLogRows(tr)}</div>
        </div>
        <p class="text-xs text-gray-400 text-right">Created: ${createdAt}</p>
    </div>`;
}

function buildItemRows(tr) {
    if (!tr.items || tr.items.length === 0) {
        return `<tr><td colspan="5" class="text-center text-gray-400 py-4">Tidak ada item.</td></tr>`;
    }
    return tr.items.map(item => {
        const name           = item.product ? item.product.product_name : '-';
        const qty            = parseInt(item.quantity);
        const delQty         = parseInt(item.delivery_quantity);
        const recQty         = parseInt(item.received_qty);
        const fullyLocked    = !!item.fully_locked;
        const deliveryLocked = !!item.delivery_locked;

        const delClass = delQty > qty ? 'border-red-400 bg-red-50' : (delQty > 0 && delQty < qty) ? 'border-orange-400 bg-orange-50' : '';
        const recClass = recQty > qty ? 'border-red-400 bg-red-50' : (recQty > 0 && recQty < qty) ? 'border-orange-400 bg-orange-50' : '';
        const warnHtml = item.qty_warning
            ? `<div class="w-full mt-1 flex items-center gap-1 text-orange-600 text-xs font-medium">⚠ ${item.qty_warning}</div>`
            : '';

        if (fullyLocked) {
            const ib = statusColors[item.item_status] || 'badge-ghost';
            return `<tr class="border-b border-gray-100 bg-green-50/40">
                <td class="text-sm font-medium text-gray-700 align-middle py-3">${name}
                    <div class="mt-1"><span class="badge badge-success badge-xs">🔒 Accepted</span></div></td>
                <td class="text-center text-sm font-semibold">${qty}</td>
                <td colspan="3"><div class="flex items-center gap-4 py-2 px-1 flex-wrap">
                    <div class="flex flex-col gap-0.5"><span class="text-xs text-gray-400">Delivery Qty</span><span class="text-sm font-semibold bg-gray-100 rounded px-2 py-0.5">${delQty}</span></div>
                    <div class="flex flex-col gap-0.5"><span class="text-xs text-gray-400">Received Qty</span><span class="text-sm font-semibold bg-gray-100 rounded px-2 py-0.5">${recQty}</span></div>
                    <div class="flex flex-col gap-0.5"><span class="text-xs text-gray-400">Status</span><span class="badge ${ib} badge-sm capitalize">${statusLabels[item.item_status] || item.item_status}</span></div>
                </div>${warnHtml}</td>
            </tr>`;
        }

        const lockSvg = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>`;
        const delAttr = deliveryLocked
            ? `readonly class="input input-xs input-bordered w-20 text-center bg-gray-100 cursor-not-allowed ${delClass}" title="Delivery qty terkunci"`
            : `class="input input-xs input-bordered w-20 text-center ${delClass}" title="Request: ${qty}"`;

        return `<tr class="border-b border-gray-100 ${item.qty_warning ? 'bg-orange-50/30' : ''}">
            <td class="text-sm font-medium text-gray-700 align-top pt-3">${name}</td>
            <td class="text-center text-sm font-semibold align-top pt-3">${qty}</td>
            <td colspan="3">
                <form method="POST" action="/stocktransfer/item/${item.id}/status"
                      onsubmit="submitItemUpdate(event,this,${item.id})" class="flex items-start gap-2 flex-wrap py-1">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PATCH">
                    <div class="flex flex-col gap-0.5">
                        <label class="text-xs ${deliveryLocked ? 'text-gray-400' : 'text-gray-500'} whitespace-nowrap flex items-center gap-1">Delivery Qty ${deliveryLocked ? lockSvg : ''}</label>
                        <input type="number" name="delivery_quantity" value="${delQty}" ${delAttr} min="0"/>
                    </div>
                    <div class="flex flex-col gap-0.5">
                        <label class="text-xs text-gray-500 whitespace-nowrap">Received Qty</label>
                        <input type="number" name="received_qty" value="${recQty}" class="input input-xs input-bordered w-20 text-center ${recClass}" min="0" title="Request: ${qty}"/>
                    </div>
                    <div class="flex flex-col gap-0.5 flex-1">
                        <label class="text-xs text-gray-500 whitespace-nowrap">Status</label>
                        <select name="item_status" class="select select-xs select-bordered">${buildStatusOptions(item.item_status)}</select>
                    </div>
                    <div class="flex flex-col gap-0.5 justify-end">
                        <label class="text-xs text-transparent select-none">-</label>
                        <button type="submit" class="btn btn-xs bg-sky-500 hover:bg-sky-600 text-white border-none">Simpan</button>
                    </div>
                    ${warnHtml}
                </form>
            </td>
        </tr>`;
    }).join('');
}

function buildLogRows(tr) {
    if (!tr.logs || tr.logs.length === 0) {
        return '<p class="text-sm text-gray-400 text-center py-4">Belum ada aktivitas tercatat.</p>';
    }
    return tr.logs.map((log, idx) => {
        const isFirst   = idx === 0;
        const typeLabel = log.type === 'transfer' ? 'Transfer' : `Item: ${log.item_name || '-'}`;
        const typeColor = log.type === 'transfer' ? 'bg-sky-100 text-sky-700' : 'bg-purple-100 text-purple-700';
        const badgeOld  = log.old_status
            ? `<span class="badge ${statusColors[log.old_status] || 'badge-ghost'} badge-xs capitalize">${statusLabels[log.old_status] || log.old_status}</span>`
            : `<span class="text-gray-400 text-xs italic">—</span>`;
        const badgeNew  = `<span class="badge ${statusColors[log.new_status] || 'badge-ghost'} badge-xs capitalize">${statusLabels[log.new_status] || log.new_status}</span>`;
        const icon      = log.type === 'transfer'
            ? `<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ${isFirst?'text-white':'text-gray-500'}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>`
            : `<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ${isFirst?'text-white':'text-gray-500'}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>`;
        return `<div class="flex gap-3">
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full ${isFirst?'bg-sky-500':'bg-gray-200'} flex items-center justify-center shrink-0">${icon}</div>
                ${idx < tr.logs.length-1 ? '<div class="w-0.5 bg-gray-200 flex-1 my-1"></div>' : ''}
            </div>
            <div class="pb-4 flex-1">
                <div class="flex items-center gap-2 flex-wrap mb-1">
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full ${typeColor}">${typeLabel}</span>
                    <span class="text-xs text-gray-400">${log.created_at || ''}</span>
                </div>
                <div class="flex items-center gap-2 flex-wrap">${badgeOld} → ${badgeNew}</div>
                <p class="text-xs text-gray-500 mt-1">👤 ${log.user_name}</p>
            </div>
        </div>`;
    }).join('');
}
</script>
@endsection
