@extends('layouts.app')

@section('title', 'Purchase Order')

@section('content')
    <div
        class="bg-base-100 p-6 h-full font-sans"
        x-data="{ openSearch: false, search: '{{ request('search') }}' }"
    >
        {{-- Header & Buttons --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-sky-600 flex items-center gap-2">
                Purchase Order
                <span
                    class="tooltip tooltip-right"
                    data-tip="Purchase Order Information"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-5 h-5 text-sky-400 cursor-pointer"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"
                        />
                    </svg>
                </span>
            </h1>
            <div class="flex gap-3">
                <button
                    onclick="export_modal.showModal()"
                    class="btn btn-outline btn-info btn-sm text-sky-600 rounded-lg"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-4 h-4 mr-1"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"
                        />
                    </svg>
                    Export Data
                </button>
                <button
                    onclick="import_modal.showModal()"
                    class="btn btn-outline btn-sm text-gray-500"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Import Data
                </button>
                <a
                    href="{{ route('addpurchaseorder') }}"
                    class="btn btn-sm bg-gray-100 text-gray-600 border-none hover:bg-gray-200"
                >
                    Create New
                </a>
            </div>
        </div>

        {{-- Flash Alerts --}}
        @if (session('import_success'))
            <div class="alert alert-success mb-4 flex items-start gap-3 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <div>
                    <p class="font-semibold">{{ session('import_success') }}</p>
                    @if (session('import_errors'))
                        <ul class="text-sm mt-1 list-disc list-inside text-yellow-700">
                            @foreach (session('import_errors') as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-error mb-4 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('status_success'))
            <div class="alert alert-info mb-4 flex items-center gap-3 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span class="font-semibold">{{ session('status_success') }}</span>
            </div>
        @endif

        {{-- Tabs Status --}}
        <div class="mb-6 overflow-x-auto">
            <div
                class="flex space-x-6 text-sm text-gray-500 border-b border-gray-200 pb-2 min-w-max"
            >
                @php
                    $tabs = ['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];
                    $currentStatus = $status ?? 'all';
                @endphp

                @foreach ($tabs as $slug => $label)
                    <a
                        href="{{ route('purchaseorder', array_merge(['status' => $slug === 'all' ? null : $slug], request()->except('status'))) }}"
                        class="px-1 pb-2 {{ $currentStatus == $slug ? 'text-sky-600 font-semibold border-b-2 border-sky-600' : 'hover:text-gray-700' }}"
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Toolbar --}}
        <div
            class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6"
        >
            {{-- Per Page Form --}}
            <form
                action="{{ url()->current() }}"
                method="GET"
                id="perPageForm"
                class="flex items-center gap-3"
            >
                <select
                    class="select appearance-none border-2 border-gray-200 rounded-lg"
                    name="per_page"
                    onchange="this.form.submit()"
                >
                    <option
                        value="5"
                        {{ request('per_page') == 5 ? 'selected' : '' }}
                    >
                        5 Rows
                    </option>
                    <option
                        value="10"
                        {{ request('per_page', 10) == 10 ? 'selected' : '' }}
                    >
                        10 Rows
                    </option>
                    <option
                        value="25"
                        {{ request('per_page') == 25 ? 'selected' : '' }}
                    >
                        25 Rows
                    </option>
                    <option
                        value="50"
                        {{ request('per_page') == 50 ? 'selected' : '' }}
                    >
                        50 Rows
                    </option>
                </select>
            </form>

            <div class="flex items-center gap-3 w-full md:w-auto">
                {{-- Search Bar --}}
                <div class="relative w-full md:w-80">
                    <form
                        action="{{ url()->current() }}"
                        method="GET"
                        id="searchForm"
                    >
                        {{-- Bawa filter tanggal/status saat search --}}
                        @foreach (request()->except(['search', 'search_by']) as $k => $v)
                            <input
                                type="hidden"
                                name="{{ $k }}"
                                value="{{ $v }}"
                            />
                        @endforeach

                        <div class="relative flex items-center">
                            <input
                                type="text"
                                name="search"
                                x-model="search"
                                @input="openSearch = search.length > 0"
                                @focus="if(search.length > 0) openSearch = true"
                                @click.away="openSearch = false"
                                placeholder="Search purchase orders..."
                                class="input input-sm input-bordered w-full pr-16 rounded-full focus:outline-sky-500"
                                autocomplete="off"
                            />
                            <input
                                type="hidden"
                                name="search_by"
                                id="searchBy"
                            />
                            <div
                                class="absolute inset-y-0 right-0 flex items-center pr-2 gap-1"
                            >
                                <template x-if="search.length > 0">
                                    <button
                                        type="button"
                                        @click="window.location.href='{{ url()->current() }}'"
                                        class="p-1 text-gray-400 hover:text-red-500"
                                    >
                                        ✕
                                    </button>
                                </template>
                                <svg
                                    class="w-4 h-4 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                        stroke-width="2"
                                    />
                                </svg>
                            </div>
                        </div>

                        {{-- Suggestions --}}
                        <div
                            x-show="openSearch"
                            class="absolute z-50 w-full mt-2 bg-white border rounded-xl shadow-xl"
                        >
                            <button
                                type="submit"
                                @click="document.getElementById('searchBy').value = 'po_number'"
                                class="w-full px-4 py-2 text-sm text-left hover:bg-sky-50 rounded-t-xl"
                            >
                                PO Number:
                                <span
                                    x-text="search"
                                    class="font-bold"
                                ></span>
                            </button>
                            <button
                                type="submit"
                                @click="document.getElementById('searchBy').value = 'product'"
                                class="w-full px-4 py-2 text-sm text-left hover:bg-sky-50"
                            >
                                Product:
                                <span
                                    x-text="search"
                                    class="font-bold"
                                ></span>
                            </button>
                            <button
                                type="submit"
                                @click="document.getElementById('searchBy').value = 'vendor'"
                                class="w-full px-4 py-2 text-sm text-left hover:bg-sky-50 rounded-b-xl"
                            >
                                Vendor:
                                <span
                                    x-text="search"
                                    class="font-bold"
                                ></span>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Trigger Modal Filter --}}
                <button
                    onclick="filter_modal.showModal()"
                    class="btn btn-sm btn-outline border-gray-300 font-normal normal-case"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4 mr-2"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.5a1 1 0 01-.293.707L13 14.414V19a1 1 0 01-.447.894l-4 2.667A1 1 0 017 21.667V14.414L3.293 7.207A1 1 0 013 6.5V4z"
                        />
                    </svg>
                    All Filter
                    @if (request()->anyFilled(['start_date', 'end_date']))
                        <span class="badge badge-info badge-xs ml-1">!</span>
                    @endif
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="border rounded-lg bg-white overflow-hidden">
            <table class="table w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th>PO Number</th>
                        <th>Product</th>
                        <th>Warehouse</th>
                        <th>Qty</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchaseOrders as $po)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="font-medium whitespace-nowrap">
                                <a href="{{ route('purchaseorder.detail', $po->id_purchase_order) }}" class="text-sky-600 hover:text-sky-800 hover:underline">
                                    {{ $po->po_number ?? 'PO-' . str_pad($po->id_purchase_order, 5, '0', STR_PAD_LEFT) }}
                                </a>
                            </td>
                            <td>{{ $po->product->product_name ?? '-' }}</td>
                            <td class="text-xs">
                                {{ $po->warehouse->warehouse_name ?? '-' }}
                            </td>
                            <td>{{ $po->stock }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending'   => 'badge-warning',
                                        'approved'  => 'badge-info',
                                        'completed' => 'badge-success',
                                        'cancelled' => 'badge-error',
                                    ];
                                    $badgeClass = $statusColors[strtolower($po->status ?? '')] ?? 'badge-ghost';
                                @endphp
                                <span
                                    class="badge {{ $badgeClass }} badge-sm capitalize"
                                >
                                    {{ $po->status ?? 'pending' }}
                                </span>
                            </td>
                            <td class="text-xs text-gray-500">
                                {{ $po->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="text-center">
                                <button
                                    onclick="document.getElementById('po_detail_modal_{{ $po->id_purchase_order }}').showModal()"
                                    class="btn btn-xs btn-outline btn-info gap-1 rounded-lg"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($purchaseOrders->isEmpty())
                <div class="p-10 text-center text-gray-400">
                    No purchase orders found.
                </div>
            @endif
        </div>

        {{-- Pagination --}}
        <div
            class="flex justify-between items-center mt-4 pb-4 text-xs text-gray-500"
        >
            {{-- Info Data --}}
            <div>
                Showing {{ $purchaseOrders->firstItem() ?? 0 }} to
                {{ $purchaseOrders->lastItem() ?? 0 }} of
                {{ $purchaseOrders->total() }} entries
            </div>

            {{-- Navigasi Tombol --}}
            <div class="flex items-center gap-2">
                {{-- Tombol Previous --}}
                @if ($purchaseOrders->onFirstPage())
                    <button class="btn btn-sm btn-ghost text-gray-400" disabled>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-4 h-4"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15.75 19.5L8.25 12l7.5-7.5"
                            />
                        </svg>
                    </button>
                @else
                    <a
                        href="{{ $purchaseOrders->appends(request()->query())->previousPageUrl() }}"
                        class="btn btn-sm btn-ghost text-sky-500 hover:bg-sky-50"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-4 h-4"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15.75 19.5L8.25 12l7.5-7.5"
                            />
                        </svg>
                    </a>
                @endif

                {{-- Nomor Halaman --}}
                @foreach ($purchaseOrders->getUrlRange(1, $purchaseOrders->lastPage()) as $page => $url)
                    @if ($page == $purchaseOrders->currentPage())
                        <button
                            class="btn btn-sm bg-sky-500 text-white border-none hover:bg-sky-600"
                        >
                            {{ $page }}
                        </button>
                    @else
                        <a
                            href="{{ $url . '&' . http_build_query(request()->except('page')) }}"
                            class="btn btn-sm btn-outline border-gray-200 text-gray-500 hover:bg-gray-50"
                        >
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                {{-- Tombol Next --}}
                @if ($purchaseOrders->hasMorePages())
                    <a
                        href="{{ $purchaseOrders->appends(request()->query())->nextPageUrl() }}"
                        class="btn btn-sm btn-ghost text-sky-500 hover:bg-sky-50"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-4 h-4"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M8.25 4.5l7.5 7.5-7.5 7.5"
                            />
                        </svg>
                    </a>
                @else
                    <button class="btn btn-sm btn-ghost text-gray-400" disabled>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-4 h-4"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M8.25 4.5l7.5 7.5-7.5 7.5"
                            />
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL IMPORT --}}
    <dialog id="import_modal" class="modal">
        <div class="modal-box w-11/12 max-w-lg bg-white p-0">
            {{-- Header --}}
            <div class="p-6 border-b flex justify-between items-center">
                <h3 class="font-bold text-xl text-gray-700 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-sky-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Import Purchase Order
                </h3>
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost">✕</button>
                </form>
            </div>

            {{-- Body --}}
            <div class="p-6 space-y-5">
                {{-- Download Template --}}
                <div class="bg-sky-50 border border-sky-200 rounded-xl p-4 flex items-center justify-between gap-4">
                    <div>
                        <p class="font-semibold text-sky-800 text-sm">Download Template Excel</p>
                        <p class="text-xs text-sky-600 mt-0.5">Isi template ini lalu upload di bawah.</p>
                    </div>
                    <a
                        href="{{ route('purchaseorder.template') }}"
                        class="btn btn-sm bg-sky-500 hover:bg-sky-600 text-white border-none gap-2 shrink-0"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Download Template
                    </a>
                </div>

                {{-- Format Info --}}
                <div class="text-xs text-gray-500 space-y-1 bg-gray-50 rounded-lg p-3">
                    <p class="font-semibold text-gray-600 mb-1">Format kolom template:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        <li><span class="font-medium text-gray-700">Product Name*</span> — Nama produk (sesuai nama di sistem)</li>
                        <li><span class="font-medium text-gray-700">Warehouse Name*</span> — Nama gudang</li>
                        <li><span class="font-medium text-gray-700">Qty*</span> — Jumlah (angka positif)</li>
                        <li><span class="font-medium text-gray-700">Status</span> — pending / approved / completed / cancelled</li>
                    </ul>
                    <p class="text-yellow-600 font-medium mt-2">* Kolom wajib diisi. Import dimulai dari baris ke-3 (baris 1 = header, baris 2 = keterangan).</p>
                </div>

                {{-- Upload Form --}}
                <form
                    action="{{ route('purchaseorder.import') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    id="importForm"
                >
                    @csrf
                    <div
                        class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-sky-400 transition-colors cursor-pointer"
                        onclick="document.getElementById('import_file').click()"
                        id="dropzone"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        <p class="text-sm text-gray-500" id="dropzone_label">Klik untuk pilih file atau drag & drop</p>
                        <p class="text-xs text-gray-400 mt-1">Format: .xlsx, .xls, .csv — Maks. 5MB</p>
                        <input
                            type="file"
                            name="import_file"
                            id="import_file"
                            accept=".xlsx,.xls,.csv"
                            class="hidden"
                            onchange="updateDropzoneLabel(this)"
                        />
                    </div>

                    <div class="mt-4 flex justify-end gap-3">
                        <form method="dialog">
                            <button type="submit" class="btn btn-sm btn-ghost text-gray-500">Batal</button>
                        </form>
                        <button
                            type="submit"
                            form="importForm"
                            class="btn btn-sm bg-sky-500 hover:bg-sky-600 text-white border-none px-6"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            Import Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    <script>
        function updateDropzoneLabel(input) {
            const label = document.getElementById('dropzone_label');
            if (input.files && input.files[0]) {
                label.textContent = '📄 ' + input.files[0].name;
                label.classList.add('text-sky-600', 'font-medium');
            }
        }
        // Drag & Drop
        const dz = document.getElementById('dropzone');
        if (dz) {
            dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('border-sky-500','bg-sky-50'); });
            dz.addEventListener('dragleave', () => { dz.classList.remove('border-sky-500','bg-sky-50'); });
            dz.addEventListener('drop', e => {
                e.preventDefault();
                dz.classList.remove('border-sky-500','bg-sky-50');
                const input = document.getElementById('import_file');
                input.files = e.dataTransfer.files;
                updateDropzoneLabel(input);
            });
        }
    </script>

    {{-- MODAL EXPORT --}}
    <dialog id="export_modal" class="modal">
        <div class="modal-box w-11/12 max-w-md bg-white p-0">
            {{-- Header --}}
            <div class="p-6 border-b flex justify-between items-center">
                <h3 class="font-bold text-xl text-gray-700 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                    Export Purchase Order
                </h3>
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost">✕</button>
                </form>
            </div>

            {{-- Body --}}
            <form
                action="{{ route('purchaseorder.export') }}"
                method="GET"
                id="exportForm"
            >
                <div class="p-6 space-y-5">
                    {{-- Info --}}
                    <div class="bg-green-50 border border-green-200 rounded-xl p-3 text-sm text-green-800">
                        <p class="font-semibold mb-0.5">Data yang akan diexport:</p>
                        <p class="text-xs text-green-700">Semua Purchase Order sesuai rentang tanggal yang dipilih. Jika tanggal kosong, semua data akan diexport.</p>
                    </div>

                    {{-- Date Range --}}
                    <div class="grid grid-cols-1 gap-4">
                        <div class="form-control">
                            <label class="label pb-1">
                                <span class="label-text font-semibold text-gray-700">Tanggal Mulai (Start From)</span>
                            </label>
                            <input
                                type="date"
                                name="start_date"
                                class="input input-bordered w-full focus:outline-sky-400"
                                value="{{ request('start_date') }}"
                            />
                        </div>
                        <div class="form-control">
                            <label class="label pb-1">
                                <span class="label-text font-semibold text-gray-700">Tanggal Akhir (End)</span>
                            </label>
                            <input
                                type="date"
                                name="end_date"
                                class="input input-bordered w-full focus:outline-sky-400"
                                value="{{ request('end_date') }}"
                            />
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="p-6 border-t flex justify-end items-center gap-3">
                    <form method="dialog">
                        <button type="submit" class="btn btn-sm btn-ghost text-gray-500">Batal</button>
                    </form>
                    <button
                        type="submit"
                        form="exportForm"
                        class="btn btn-sm bg-green-600 hover:bg-green-700 text-white border-none px-6 gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                        </svg>
                        Export Excel
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    {{-- MODAL FILTER --}}
    <dialog id="filter_modal" class="modal">
        <div class="modal-box w-11/12 max-w-2xl bg-white p-0 overflow-visible">
            <div class="p-6 border-b flex justify-between items-center">
                <h3 class="font-bold text-2xl text-gray-700">Filter</h3>
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost">✕</button>
                </form>
            </div>

            {{-- FORM FILTER --}}
            <form
                action="{{ route('purchaseorder', ['status' => $status ?? 'all']) }}"
                method="GET"
            >
                <input
                    type="hidden"
                    name="search"
                    value="{{ request('search') }}"
                />
                <input
                    type="hidden"
                    name="per_page"
                    value="{{ request('per_page') }}"
                />

                <div class="p-6 space-y-6">
                    {{-- Date Range --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label font-bold text-gray-700">
                                Created Date From
                            </label>
                            <input
                                type="date"
                                name="start_date"
                                class="input input-bordered w-full"
                                value="{{ request('start_date') }}"
                            />
                        </div>
                        <div class="form-control">
                            <label class="label font-bold text-gray-700">
                                Created Date To
                            </label>
                            <input
                                type="date"
                                name="end_date"
                                class="input input-bordered w-full"
                                value="{{ request('end_date') }}"
                            />
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t flex justify-end items-center gap-4">
                    <a
                        href="{{ route('purchaseorder', ['status' => $status ?? 'all']) }}"
                        class="text-sky-500 font-semibold"
                    >
                        Reset Filter
                    </a>
                    <button
                        type="submit"
                        class="btn bg-sky-500 hover:bg-sky-600 text-white border-none rounded-full px-10"
                    >
                        Apply Filter
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    {{-- MODAL DETAIL & UPDATE STATUS PO --}}
    @foreach ($purchaseOrders as $po)
        <dialog id="po_detail_modal_{{ $po->id_purchase_order }}" class="modal">
            <div class="modal-box w-11/12 max-w-lg bg-white p-0">
                {{-- Header --}}
                <div class="p-6 border-b flex justify-between items-center">
                    <h3 class="font-bold text-xl text-gray-700 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-sky-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        Detail Purchase Order
                    </h3>
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost">✕</button>
                    </form>
                </div>

                {{-- Body --}}
                <div class="p-6 space-y-4">
                    {{-- Info Grid --}}
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">PO Number</p>
                            <p class="font-semibold text-gray-800 mt-0.5">{{ $po->po_number ?? 'PO-' . str_pad($po->id_purchase_order, 5, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Product</p>
                            <p class="font-semibold text-gray-800 mt-0.5">{{ $po->product->product_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Warehouse</p>
                            <p class="font-semibold text-gray-800 mt-0.5">{{ $po->warehouse->warehouse_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Quantity</p>
                            <p class="font-semibold text-gray-800 mt-0.5">{{ $po->stock }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Created Date</p>
                            <p class="font-semibold text-gray-800 mt-0.5">{{ $po->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Current Status</p>
                            @php
                                $statusColors = [
                                    'pending'   => 'badge-warning',
                                    'approved'  => 'badge-info',
                                    'completed' => 'badge-success',
                                    'cancelled' => 'badge-error',
                                ];
                                $badgeClass = $statusColors[strtolower($po->status ?? '')] ?? 'badge-ghost';
                            @endphp
                            <span class="badge {{ $badgeClass }} badge-sm capitalize mt-1">{{ $po->status ?? 'pending' }}</span>
                        </div>
                    </div>

                    <div class="divider my-2"></div>

                    {{-- Update Status Form --}}
                    @php
                        $isLocked = in_array(strtolower($po->status ?? 'pending'), ['completed', 'cancelled']);
                    @endphp
                    <form
                        action="{{ route('purchaseorder.updateStatus', $po->id_purchase_order) }}"
                        method="POST"
                    >
                        @csrf
                        @method('PATCH')
                        <div class="form-control">
                            <label class="label pb-1">
                                <span class="label-text font-semibold text-gray-700">
                                    @if ($isLocked)
                                        Status Dokumen (Terkunci)
                                    @else
                                        Update Status Dokumen
                                    @endif
                                </span>
                            </label>
                            <select name="status" class="select select-bordered w-full focus:outline-sky-400" {{ $isLocked ? 'disabled' : '' }}>
                                @php
                                    $poStatuses = ['pending' => 'Pending', 'approved' => 'Approved', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];
                                @endphp
                                @foreach ($poStatuses as $val => $label)
                                    <option value="{{ $val }}" {{ strtolower($po->status ?? 'pending') == $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-5 flex justify-end gap-3">
                            <button type="button" onclick="this.closest('dialog').close()" class="btn btn-sm btn-ghost text-gray-500">Batal</button>
                            @if (!$isLocked)
                                <button type="submit" class="btn btn-sm bg-sky-500 hover:bg-sky-600 text-white border-none px-6 gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                    Update Status
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop"><button>close</button></form>
        </dialog>
    @endforeach
@endsection
