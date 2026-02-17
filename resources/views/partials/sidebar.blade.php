<!-- Sidebar -->
<aside
    class="w-64 min-w-[16rem] bg-white shadow-md flex flex-col hidden md:flex z-10 transition-all duration-300 h-[100vh] sticky top-0">
    <!-- Brand -->
    <div class="p-4 border-b border-gray-100 flex items-center justify-center gap-2">
        {{-- <div class="w-8 h-8 rounded bg-blue-500 flex items-center justify-center text-white font-bold text-xl">
        </div> --}}
        <a href="">
            <span class="text-xl font-bold text-blue-600">Simpan</span>
            <span class="text-xl font-bold text-yellow-600">Jual.</span>
        </a>
    </div>

    <!-- User Selector -->
    {{-- <div class="p-4">
        <select name="brand_id" class="select appearance-none">
            <option disabled selected>Brand</option>
            @foreach ($brands as $brand)
            <option value="{{ $brand->id }}" {{ request('brand_id')==$brand->id ? 'selected' : '' }}>{{
                $brand->brand_name }}</option>
            @endforeach
        </select>
    </div> --}}

    <div class="p-4">
        <form action="{{ route('set-brand') }}" method="POST">
            @csrf
            <select name="brand_id" class="select appearance-none" onchange="this.form.submit()">
                <option value="" disabled {{ session('brand_id') ? '' : 'selected' }}>Pilih Brand</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id_brand }}" {{ session('brand_id') == $brand->id_brand ? 'selected' : '' }}>
                        {{ $brand->brand_name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Menu -->
    <div class="overflow-y-auto flex-1">
        <ul class="menu flex-1 p-2 text-base-content/70">
            <li>
                <a href="{{route('addbrand')}}">Add Brand</a>
            </li>
            <li>
                <a href="{{route('addproduct')}}">Add Product</a>
            </li>
            <li>
                <a href="{{route('addwarehouse')}}">Add Warehouse</a>
            </li>
            <li>
                <a href="{{route('adjuststock')}}">Adjust Stock</a>
            </li>
            <li>
                <a href="{{route('addpurchaseorder')}}">Add Purchase Order</a>
            </li>
            <!-- Jual.Praktis Group -->
            <li class="menu-title mt-2">
                <span class="flex items-center gap-2 text-yellow-600 font-bold">
                    Jual
                    <!-- <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg> -->
                </span>
            </li>

            <!-- Dashboard -->
            <li>
                <details {{request()->is('dashboard*') ? 'open' : ''}}>
                    <summary class="group {{request()->is('dashboard*') ? 'active font-bold' : ''}}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        Dashboard
                    </summary>
                    <ul>
                        <li><a href="{{route('dashboard.inbound')}}"
                                class="{{Route::is('dashboard.inbound*') ? 'active text-blue-600 border-l-4 border-blue-600 bg-blue-50' : ''}}">Inbound</a>
                        </li>
                        <li><a href="{{route('dashboard.outbound')}}"
                                class="{{Route::is('dashboard.outbound') ? 'active text-blue-600 border-l-4 border-blue-600 bg-blue-50' : ''}}">Outbound</a>
                        </li>
                        <li><a href="{{route('dashboard.stockledger')}}"
                                class="{{Route::is('dashboard.stockledger') ? 'active text-blue-600 border-l-4 border-blue-600 bg-blue-50' : ''}}">Stock
                                Ledger</a></li>
                    </ul>
                </details>
            </li>

            <!-- Sales Order -->
            <li>
                <a href="{{route('salesorder')}}" class="{{request()->is('salesorder') ? 'active font-bold' : ''}}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Sales Order
                </a>
            </li>

            <!-- Purchase Order -->
            <li>
                <a href="{{route('purchaseorder')}}"
                    class="{{request()->is('purchaseorder') ? 'active font-bold' : ''}}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Purchase Order
                </a>
            </li>

            <!-- Stock Transfer -->
            <li>
                <a href="{{route('stocktransfer')}}"
                    class="{{request()->is('stocktransfer') ? 'active font-bold' : ''}}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    Stock Transfer
                </a>
            </li>

            <!-- Withdrawal Request -->
            <li>
                <a href="{{route('withdrawalrequest')}}"
                    class="{{request()->is('withdrawalrequest') ? 'active font-bold' : ''}}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                    Withdrawal Request
                </a>
            </li>

            <!-- Inventory Management -->
            <li>
                <details {{request()->is('inventory*') ? 'open' : ''}}>
                    <summary class="group {{request()->is('inventory*') ? 'active font-bold' : ''}}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Inventory Management
                    </summary>
                    <ul>
                        <li><a href="{{route('inventory.stockmonitoring')}}"
                                class="{{Route::is('inventory.stockmonitoring') ? 'active text-blue-600 border-l-4 border-blue-600 bg-blue-50' : ''}}">Stock
                                Monitoring</a></li>
                        <li><a href="{{route('inventory.safetystock')}}"
                                class="{{Route::is('inventory.safetystock') ? 'active text-blue-600 border-l-4 border-blue-600 bg-blue-50' : ''}}">Safety
                                Stock</a></li>
                        <li><a href="{{route('inventory.preorderstock')}}"
                                class="{{Route::is('inventory.preorderstock') ? 'active text-blue-600 border-l-4 border-blue-600 bg-blue-50' : ''}}">Pre-order
                                Stock</a></li>
                        <li><a href="{{route('inventory.bundlestock')}}"
                                class="{{Route::is('inventory.bundlestock') ? 'active text-blue-600 border-l-4 border-blue-600 bg-blue-50' : ''}}">Bundle
                                Stock <span class="badge badge-primary badge-xs">New</span></a></li>
                    </ul>
                </details>
            </li>

            <!-- Item Management -->
            <li>
                <details {{request()->is('item*') ? 'open' : ''}}>
                    <summary class="group {{request()->is('item*') ? 'active font-bold' : ''}}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Item Management
                    </summary>
                    <ul>
                        <li><a href="{{route('item.itemmaster')}}"
                                class="{{Route::is('item.itemmaster') ? 'active text-blue-600 border-l-4 border-blue-600 bg-blue-50' : ''}}">Item
                                Master <span class="badge badge-primary badge-xs">New</span></a></li>
                        <li><a href="{{route('item.itempublished')}}"
                                class="{{Route::is('item.itempublished') ? 'active text-blue-600 border-l-4 border-blue-600 bg-blue-50' : ''}}">Item
                                Published</a></li>
                        <li><a href="{{route('item.downloaditem')}}"
                                class="{{Route::is('item.downloaditem') ? 'active text-blue-600 border-l-4 border-blue-600 bg-blue-50' : ''}}">Download
                                Item</a></li>
                    </ul>
                </details>
            </li>
        </ul>
    </div>

    <!-- Sign Out -->
    <div class="p-4 border-t border-gray-100">
        <a href="{{ url('/logout') }}" class="flex items-center gap-2 text-red-500 hover:text-red-600 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Sign out
        </a>
    </div>
</aside>