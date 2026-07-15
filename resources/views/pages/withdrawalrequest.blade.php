@extends('layouts.app')

@section('title', 'Withdrawal Request')

@section('content')
<div class="bg-base-100 p-6 h-full font-sans" x-data="{ openSearch: false, search: '{{ request('search') }}' }">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-sky-600 flex items-center gap-2">
            Withdrawal Request
            <span class="tooltip tooltip-right" data-tip="Pengeluaran barang dari warehouse tanpa penjualan">
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
                <a href="{{ route('withdrawalrequest', array_merge(['status' => $slug === 'all' ? null : $slug], request()->except('status'))) }}"
                   class="px-1 pb-2 whitespace-nowrap {{ $currentStatus == $slug ? 'text-sky-600 font-semibold border-b-2 border-sky-600' : 'hover:text-gray-700' }}">
                    {{ $label }}
                    @if($slug !== 'all')
                        <span class="ml-1 text-xs text-gray-400">({{ $withdrawalRequests->total() > 0 && $currentStatus == $slug ? $withdrawalRequests->total() : '' }})</span>
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
                               placeholder="Cari WR Number, purpose, produk..."
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
                        <button type="submit" @click="document.getElementById('searchBy').value='wr_number'" class="w-full px-4 py-2 text-sm text-left hover:bg-sky-50">WR Number: <span x-text="search" class="font-bold"></span></button>
                        <button type="submit" @click="document.getElementById('searchBy').value='purpose'" class="w-full px-4 py-2 text-sm text-left hover:bg-sky-50">Purpose: <span x-text="search" class="font-bold"></span></button>
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
                        <th class="font-medium">WR Number</th>
                        <th class="font-medium">Status</th>
                        <th class="font-medium">Purpose</th>
                        <th class="font-medium">Source Location</th>
                        <th class="font-medium">Created At</th>
                        <th class="font-medium text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawalRequests as $wr)
                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100">
                            <td>
                                <button onclick="openDetailModal('{{ $wr->wr_number }}')"
                                        class="text-sky-600 font-semibold hover:underline">
                                    {{ $wr->wr_number }}
                                </button>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'on going' => 'badge-warning',
                                        'done'     => 'badge-success',
                                    ];
                                    $badgeClass = $statusColors[$wr->status] ?? 'badge-ghost';
                                @endphp
                                <span class="badge {{ $badgeClass }} badge-sm capitalize whitespace-nowrap">
                                    {{ $statuses[$wr->status] ?? $wr->status }}
                                </span>
                            </td>
                            <td class="text-sm text-gray-700 max-w-[200px] truncate">{{ $wr->purpose }}</td>
                            <td class="text-sm text-gray-700">{{ $wr->sourceWarehouse->warehouse_name ?? '-' }}</td>
                            <td class="text-xs text-gray-500">{{ $wr->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="openDetailModal('{{ $wr->wr_number }}')" class="btn btn-xs btn-outline btn-info" title="Detail">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5" stroke="currentColor" class="w-24 h-24 mb-4 text-gray-200">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-500">Belum ada Withdrawal Request</p>
                                    <p class="text-sm mt-1">Klik <span class="text-sky-500 font-medium">Create New</span> untuk membuat withdrawal request pertama.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($withdrawalRequests->total() > 0)
    <div class="flex justify-between items-center mt-4 pb-4 text-xs text-gray-500">
        <div>Showing {{ $withdrawalRequests->firstItem() ?? 0 }} to {{ $withdrawalRequests->lastItem() ?? 0 }} of {{ $withdrawalRequests->total() }} entries</div>
        <div class="flex items-center gap-2">
            @if($withdrawalRequests->onFirstPage())
                <button class="btn btn-sm btn-ghost text-gray-300" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                </button>
            @else
                <a href="{{ $withdrawalRequests->appends(request()->query())->previousPageUrl() }}" class="btn btn-sm btn-ghost text-sky-500 hover:bg-sky-50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                </a>
            @endif
            @foreach($withdrawalRequests->getUrlRange(1, $withdrawalRequests->lastPage()) as $page => $url)
                @if($page == $withdrawalRequests->currentPage())
                    <button class="btn btn-sm bg-sky-500 text-white border-none hover:bg-sky-600">{{ $page }}</button>
                @else
                    <a href="{{ $url . '&' . http_build_query(request()->except('page')) }}" class="btn btn-sm btn-outline border-gray-200 text-gray-500 hover:bg-gray-50">{{ $page }}</a>
                @endif
            @endforeach
            @if($withdrawalRequests->hasMorePages())
                <a href="{{ $withdrawalRequests->appends(request()->query())->nextPageUrl() }}" class="btn btn-sm btn-ghost text-sky-500 hover:bg-sky-50">
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
    <div class="modal-box w-11/12 max-w-4xl bg-white p-0">
        <div class="p-6 border-b flex justify-between items-center">
            <h3 class="font-bold text-xl text-gray-700 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-sky-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
                Buat Withdrawal Request Baru
            </h3>
            <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost">✕</button></form>
        </div>

        <form action="{{ route('withdrawalrequest.store') }}" method="POST" id="createForm">
            @csrf
            <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">

                {{-- Purpose & Source Location --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label pb-1"><span class="label-text font-semibold text-gray-700">Purpose <span class="text-red-500">*</span></span></label>
                        <input type="text" name="purpose" class="input input-bordered w-full focus:outline-sky-400" placeholder="Alasan pengeluaran barang..." required value="{{ old('purpose') }}"/>
                    </div>
                    <div class="form-control">
                        <label class="label pb-1"><span class="label-text font-semibold text-gray-700">Source Location <span class="text-red-500">*</span></span></label>
                        <select name="source_location" id="source_location" class="select select-bordered w-full focus:outline-sky-400" required>
                            <option value="" disabled selected>Pilih Gudang Asal</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id_warehouse }}" {{ old('source_location') == $wh->id_warehouse ? 'selected' : '' }}>{{ $wh->warehouse_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Shipping Info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label pb-1"><span class="label-text font-semibold text-gray-700">Nomor Resi</span></label>
                        <input type="text" name="tracking_number" class="input input-bordered w-full focus:outline-sky-400" placeholder="Nomor resi pengiriman (opsional)" value="{{ old('tracking_number') }}"/>
                    </div>
                    <div class="form-control">
                        <label class="label pb-1"><span class="label-text font-semibold text-gray-700">Nama Ekspedisi</span></label>
                        <input type="text" name="expedition_name" class="input input-bordered w-full focus:outline-sky-400" placeholder="JNE, J&T, SiCepat, dll (opsional)" value="{{ old('expedition_name') }}"/>
                    </div>
                </div>

                {{-- Recipient Detail --}}
                <div class="border border-sky-200 rounded-xl p-4 bg-sky-50/30">
                    <h4 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        Recipient Detail
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label pb-1"><span class="label-text font-semibold text-gray-700">Name <span class="text-red-500">*</span></span></label>
                            <input type="text" name="recipient_name" class="input input-bordered w-full focus:outline-sky-400" placeholder="Nama penerima" required value="{{ old('recipient_name') }}"/>
                        </div>
                        <div class="form-control">
                            <label class="label pb-1"><span class="label-text font-semibold text-gray-700">Phone <span class="text-red-500">*</span></span></label>
                            <input type="text" name="recipient_phone" class="input input-bordered w-full focus:outline-sky-400" placeholder="Nomor telepon penerima" required value="{{ old('recipient_phone') }}"/>
                        </div>
                        <div class="form-control">
                            <label class="label pb-1"><span class="label-text font-semibold text-gray-700">Email</span></label>
                            <input type="email" name="recipient_email" class="input input-bordered w-full focus:outline-sky-400" placeholder="Email penerima (opsional)" value="{{ old('recipient_email') }}"/>
                        </div>
                        <div class="form-control">
                            <label class="label pb-1"><span class="label-text font-semibold text-gray-700">Address <span class="text-red-500">*</span></span></label>
                            <textarea name="recipient_address" rows="2" class="textarea textarea-bordered w-full focus:outline-sky-400" placeholder="Alamat lengkap penerima" required>{{ old('recipient_address') }}</textarea>
                        </div>
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
                                    <th class="text-gray-500 font-medium w-24">Quantity</th>
                                    <th class="text-gray-500 font-medium w-36">Selling Price</th>
                                    <th class="text-gray-500 font-medium w-36">Total</th>
                                    <th class="w-10"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <tr id="item-row-0">
                                    <td>
                                        <select name="items[0][id_product]" class="select select-bordered select-sm w-full" required>
                                            <option value="" disabled selected>Pilih Produk</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id_product }}" data-price="{{ $p->price }}">{{ $p->product_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][quantity]" min="1" class="input input-bordered input-sm w-full" placeholder="Qty" required onchange="calcRowTotal(0)" oninput="calcRowTotal(0)"/>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][selling_price]" min="0" step="0.01" class="input input-bordered input-sm w-full" placeholder="Harga" required onchange="calcRowTotal(0)" oninput="calcRowTotal(0)"/>
                                    </td>
                                    <td>
                                        <span id="row-total-0" class="text-sm font-semibold text-gray-700">Rp 0</span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" onclick="removeRow(0)" class="btn btn-xs btn-ghost text-red-400 hover:text-red-600">✕</button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="text-right font-semibold text-gray-700">Grand Total</td>
                                    <td><span id="grand-total" class="text-sm font-bold text-sky-600">Rp 0</span></td>
                                    <td></td>
                                </tr>
                            </tfoot>
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
                    Buat Withdrawal Request
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
                Detail Withdrawal Request
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
        <form action="{{ route('withdrawalrequest', ['status' => $status ?? 'all']) }}" method="GET">
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
                <a href="{{ route('withdrawalrequest', ['status' => $status ?? 'all']) }}" class="text-sky-500 font-semibold">Reset Filter</a>
                <button type="submit" class="btn bg-sky-500 hover:bg-sky-600 text-white border-none rounded-full px-10">Apply Filter</button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>

<script>
// ────────────────────────────────────────────────────────────
// CONSTANTS
// ────────────────────────────────────────────────────────────
const statusLabels = {
    'on going': 'On Going',
    'done':     'Done',
};

const statusColors = {
    'on going': 'badge-warning',
    'done':     'badge-success',
};

// ────────────────────────────────────────────────────────────
// CREATE MODAL — ITEM ROWS
// ────────────────────────────────────────────────────────────
let rowCount = 1;
const products = @json($products->map(fn($p) => ['id' => $p->id_product, 'name' => $p->product_name, 'price' => $p->price]));

function addItemRow() {
    const idx  = rowCount++;
    const opts = products.map(p => `<option value="${p.id}" data-price="${p.price}">${p.name}</option>`).join('');
    const row  = `
    <tr id="item-row-${idx}">
        <td>
            <select name="items[${idx}][id_product]" class="select select-bordered select-sm w-full" required onchange="autoFillPrice(${idx})">
                <option value="" disabled selected>Pilih Produk</option>
                ${opts}
            </select>
        </td>
        <td>
            <input type="number" name="items[${idx}][quantity]" min="1"
                   class="input input-bordered input-sm w-full" placeholder="Qty" required onchange="calcRowTotal(${idx})" oninput="calcRowTotal(${idx})"/>
        </td>
        <td>
            <input type="number" name="items[${idx}][selling_price]" min="0" step="0.01"
                   class="input input-bordered input-sm w-full" placeholder="Harga" required onchange="calcRowTotal(${idx})" oninput="calcRowTotal(${idx})"/>
        </td>
        <td>
            <span id="row-total-${idx}" class="text-sm font-semibold text-gray-700">Rp 0</span>
        </td>
        <td class="text-center">
            <button type="button" onclick="removeRow(${idx})" class="btn btn-xs btn-ghost text-red-400 hover:text-red-600">✕</button>
        </td>
    </tr>`;
    document.getElementById('itemsTableBody').insertAdjacentHTML('beforeend', row);
}

function removeRow(idx) {
    const row = document.getElementById(`item-row-${idx}`);
    if (row && document.getElementById('itemsTableBody').children.length > 1) {
        row.remove();
        calcGrandTotal();
    }
}

function autoFillPrice(idx) {
    const sel = document.querySelector(`select[name="items[${idx}][id_product]"]`);
    const priceInput = document.querySelector(`input[name="items[${idx}][selling_price]"]`);
    if (sel && priceInput) {
        const selectedOption = sel.options[sel.selectedIndex];
        const price = selectedOption ? selectedOption.getAttribute('data-price') : 0;
        priceInput.value = price || 0;
        calcRowTotal(idx);
    }
}

function calcRowTotal(idx) {
    const qty   = parseFloat(document.querySelector(`input[name="items[${idx}][quantity]"]`)?.value) || 0;
    const price = parseFloat(document.querySelector(`input[name="items[${idx}][selling_price]"]`)?.value) || 0;
    const total = qty * price;
    const el = document.getElementById(`row-total-${idx}`);
    if (el) el.textContent = 'Rp ' + total.toLocaleString('id-ID');
    calcGrandTotal();
}

function calcGrandTotal() {
    let grand = 0;
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const m = row.id.match(/item-row-(\d+)/);
        if (m) {
            const idx   = parseInt(m[1]);
            const qty   = parseFloat(document.querySelector(`input[name="items[${idx}][quantity]"]`)?.value) || 0;
            const price = parseFloat(document.querySelector(`input[name="items[${idx}][selling_price]"]`)?.value) || 0;
            grand += qty * price;
        }
    });
    const el = document.getElementById('grand-total');
    if (el) el.textContent = 'Rp ' + grand.toLocaleString('id-ID');
}

// Auto-fill price on first row product change
document.addEventListener('DOMContentLoaded', () => {
    const firstSel = document.querySelector('select[name="items[0][id_product]"]');
    if (firstSel) firstSel.addEventListener('change', () => autoFillPrice(0));
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

// ────────────────────────────────────────────────────────────
// DETAIL MODAL
// ────────────────────────────────────────────────────────────
let currentWrNumber = null;

function openDetailModal(wrNumber) {
    currentWrNumber = wrNumber;
    const modal = document.getElementById('detail_modal');
    const body  = document.getElementById('detail_modal_body');
    body.innerHTML = '<div class="flex justify-center py-8"><span class="loading loading-spinner loading-lg text-sky-500"></span></div>';
    modal.showModal();
    fetchAndRenderDetail(wrNumber, body);
}

function reloadDetailModal() {
    if (!currentWrNumber) return;
    const body = document.getElementById('detail_modal_body');
    body.innerHTML = '<div class="flex justify-center py-4"><span class="loading loading-spinner loading-md text-sky-500"></span></div>';
    fetchAndRenderDetail(currentWrNumber, body);
}

function fetchAndRenderDetail(wrNumber, body) {
    fetch(`/withdrawalrequest/detail/${wrNumber}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => { body.innerHTML = buildDetailHTML(data); })
    .catch(err => {
        console.error(err);
        body.innerHTML = '<p class="text-red-500 text-center py-8">Gagal memuat data. Coba lagi.</p>';
    });
}

// ── AJAX: Update WR status ────────────────────────────────
async function submitWrStatus(event, formEl) {
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
            showToast(data.message, 'error');
            if (btn) btn.disabled = false;
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
    const wr         = data.withdrawal;
    const badgeCls   = statusColors[wr.status] || 'badge-ghost';
    const isLocked   = wr.status === 'done';
    const createdAt  = new Date(wr.created_at).toLocaleString('id-ID', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' });
    const sourceName = wr.source_warehouse ? wr.source_warehouse.warehouse_name : '-';

    const statusPanel = isLocked
        ? `<div class="flex items-center gap-3 p-4 border border-green-200 rounded-xl bg-green-50">
               <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-500 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
               <span class="text-sm font-semibold text-green-700">Withdrawal Request telah selesai.</span>
           </div>`
        : `<div class="flex items-center gap-3 p-4 border rounded-xl bg-sky-50">
               <span class="text-sm font-semibold text-gray-700 shrink-0">Update Status:</span>
               <form method="POST" action="/withdrawalrequest/${wr.wr_number}/status"
                     onsubmit="submitWrStatus(event,this)" class="flex items-center gap-2 flex-1">
                   <input type="hidden" name="_token" value="{{ csrf_token() }}">
                   <input type="hidden" name="_method" value="PATCH">
                   <select name="status" class="select select-sm select-bordered flex-1 max-w-xs">
                       <option value="on going" ${wr.status === 'on going' ? 'selected' : ''}>On Going</option>
                       <option value="done" ${wr.status === 'done' ? 'selected' : ''}>Done</option>
                   </select>
                   <button type="submit" class="btn btn-sm bg-sky-500 hover:bg-sky-600 text-white border-none">Update</button>
               </form>
           </div>`;

    // Item table
    let itemRows = '';
    let grandTotal = 0;
    if (wr.items && wr.items.length > 0) {
        itemRows = wr.items.map(item => {
            const name       = item.product ? item.product.product_name : '-';
            const qty        = parseInt(item.quantity);
            const sellPrice  = parseFloat(item.selling_price);
            const totalPrice = parseFloat(item.total_price);
            grandTotal      += totalPrice;
            return `<tr class="border-b border-gray-100">
                <td class="text-sm font-medium text-gray-700">${name}</td>
                <td class="text-center text-sm">${qty}</td>
                <td class="text-sm text-right">Rp ${sellPrice.toLocaleString('id-ID')}</td>
                <td class="text-sm text-right font-semibold">Rp ${totalPrice.toLocaleString('id-ID')}</td>
            </tr>`;
        }).join('');
    } else {
        itemRows = '<tr><td colspan="4" class="text-center text-gray-400 py-4">Tidak ada item.</td></tr>';
    }

    return `<div class="space-y-5">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-gray-50 rounded-xl p-4"><p class="text-xs text-gray-500 mb-1">WR Number</p><p class="font-bold text-sky-600 text-sm">${wr.wr_number}</p></div>
            <div class="bg-gray-50 rounded-xl p-4"><p class="text-xs text-gray-500 mb-1">Status</p><span class="badge ${badgeCls} badge-sm capitalize">${statusLabels[wr.status] || wr.status}</span></div>
            <div class="bg-gray-50 rounded-xl p-4"><p class="text-xs text-gray-500 mb-1">Purpose</p><p class="font-semibold text-gray-700 text-sm">${wr.purpose}</p></div>
            <div class="bg-gray-50 rounded-xl p-4"><p class="text-xs text-gray-500 mb-1">Source Location</p><p class="font-semibold text-gray-700 text-sm">${sourceName}</p></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="bg-gray-50 rounded-xl p-4"><p class="text-xs text-gray-500 mb-1">Nomor Resi</p><p class="text-sm font-medium text-gray-700">${wr.tracking_number || '-'}</p></div>
            <div class="bg-gray-50 rounded-xl p-4"><p class="text-xs text-gray-500 mb-1">Nama Ekspedisi</p><p class="text-sm font-medium text-gray-700">${wr.expedition_name || '-'}</p></div>
        </div>

        ${statusPanel}

        <div class="border border-sky-200 rounded-xl p-4 bg-sky-50/30">
            <h4 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                Recipient Detail
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div><p class="text-xs text-gray-500 mb-1">Name</p><p class="text-sm font-medium text-gray-700">${wr.recipient_name}</p></div>
                <div><p class="text-xs text-gray-500 mb-1">Phone</p><p class="text-sm font-medium text-gray-700">${wr.recipient_phone}</p></div>
                <div><p class="text-xs text-gray-500 mb-1">Email</p><p class="text-sm font-medium text-gray-700">${wr.recipient_email || '-'}</p></div>
                <div><p class="text-xs text-gray-500 mb-1">Address</p><p class="text-sm font-medium text-gray-700">${wr.recipient_address}</p></div>
            </div>
        </div>

        <div>
            <h4 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg> Item List
            </h4>
            <div class="border rounded-xl overflow-hidden overflow-x-auto">
                <table class="table table-sm w-full">
                    <thead class="bg-gray-50"><tr>
                        <th class="text-gray-500 font-medium">Nama Item</th>
                        <th class="text-gray-500 font-medium text-center w-20">Qty</th>
                        <th class="text-gray-500 font-medium text-right">Selling Price</th>
                        <th class="text-gray-500 font-medium text-right">Total</th>
                    </tr></thead>
                    <tbody>${itemRows}</tbody>
                    <tfoot class="bg-gray-50"><tr>
                        <td colspan="3" class="text-right font-semibold text-gray-700">Grand Total</td>
                        <td class="text-right font-bold text-sky-600">Rp ${grandTotal.toLocaleString('id-ID')}</td>
                    </tr></tfoot>
                </table>
            </div>
        </div>

        ${wr.notes ? `<div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl"><p class="text-xs text-yellow-700 font-semibold mb-1">Notes</p><p class="text-sm text-gray-700">${wr.notes}</p></div>` : ''}

        <p class="text-xs text-gray-400 text-right">Created: ${createdAt}</p>
    </div>`;
}
</script>
@endsection
