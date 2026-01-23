<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Praktis</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md flex flex-col hidden md:flex z-10 transition-all duration-300">
        <!-- Brand -->
        <div class="p-4 border-b border-gray-100 flex items-center gap-2">
             <div class="w-8 h-8 rounded bg-blue-500 flex items-center justify-center text-white font-bold text-xl">P</div>
             <span class="text-xl font-bold text-blue-600">Praktis.</span>
        </div>

        <!-- User Selector -->
        <div class="p-4">
            <button class="btn btn-outline btn-sm w-full justification-between flex">
                <span>Baneska</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
        </div>

        <!-- Menu -->
        <ul class="menu flex-1 overflow-y-auto p-2 text-base-content/70">
            <!-- Jual.Praktis Group -->
            <li class="menu-title mt-2">
                <span class="flex items-center gap-2 text-orange-500 font-bold">
                    Jual.Praktis
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                </span>
            </li>

            <!-- Dashboard -->
            <li>
                <details open>
                    <summary class="group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                        Dashboard
                    </summary>
                    <ul>
                        <li><a href="#" class="active text-blue-600 border-l-4 border-blue-600 bg-blue-50">Inbound</a></li>
                        <li><a href="#">Outbound</a></li>
                        <li><a href="#">Stock Ledger</a></li>
                    </ul>
                </details>
            </li>

            <!-- Sales Order -->
            <li>
                <a href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    Sales Order
                </a>
            </li>

            <!-- Purchase Order -->
            <li>
                <a href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Purchase Order
                </a>
            </li>

            <!-- Stock Transfer -->
            <li>
                <a href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                    Stock Transfer
                </a>
            </li>

            <!-- Withdrawal Request -->
            <li>
                <a href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                    Withdrawal Request
                </a>
            </li>

            <!-- Inventory Management -->
            <li>
                <details>
                    <summary>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        Inventory Management
                    </summary>
                    <ul>
                        <li><a href="#">Stock Monitoring</a></li>
                        <li><a href="#">Safety Stock</a></li>
                        <li><a href="#">Pre-order Stock</a></li>
                        <li><a href="#">Bundle Stock <span class="badge badge-primary badge-xs">New</span></a></li>
                    </ul>
                </details>
            </li>

            <!-- Item Management -->
            <li>
                <details>
                    <summary>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        Item Management
                    </summary>
                    <ul>
                        <li><a href="#">Item Master <span class="badge badge-primary badge-xs">New</span></a></li>
                        <li><a href="#">Item Published</a></li>
                        <li><a href="#">Download Item</a></li>
                    </ul>
                </details>
            </li>
        </ul>

         <!-- Sign Out -->
         <div class="p-4 border-t border-gray-100">
             <a href="{{ url('/logout') }}" class="flex items-center gap-2 text-red-500 hover:text-red-600 font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                Sign out
             </a>
         </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Header -->
        <header class="navbar bg-white shadow-sm z-10">
            <div class="flex-1">
                <h1 class="text-xl font-bold text-blue-500 px-4">Inbound</h1>
            </div>
            <div class="flex-none gap-4 px-4">
                <button class="btn btn-ghost btn-circle">
                    <div class="indicator">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                        <span class="badge badge-xs badge-primary indicator-item"></span>
                    </div>
                </button>
                <div class="flex items-center gap-2">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-bold">Bagas malatex</div>
                        <div class="text-xs text-gray-500">bagasvagas22@gmail...</div>
                    </div>
                    <div class="avatar">
                        <div class="w-10 rounded-full">
                            <img src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp" />
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Page -->
        <main class="flex-1 overflow-y-auto p-6">
            <!-- Filters -->
            <div class="flex gap-4 mb-6">
                <select class="select select-bordered select-sm w-full max-w-xs">
                    <option>Warehouse</option>
                </select>
                <select class="select select-bordered select-sm w-full max-w-xs">
                    <option>Item Code</option>
                </select>
            </div>

            <!-- Inbound Content -->
            <h2 class="text-lg font-bold text-gray-500 mb-4 uppercase">Order Level</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Card 1 -->
                <div class="card bg-base-100 shadow-sm border border-gray-200">
                    <div class="card-body p-4 h-40 flex flex-col items-center justify-center text-center">
                         <h3 class="w-full text-left text-xs font-bold text-gray-500 absolute top-4 left-4">Incoming PO</h3>
                         <div class="text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            <span class="text-xs">There was a problem displaying this chart.</span>
                         </div>
                    </div>
                </div>
                 <!-- Card 2 -->
                 <div class="card bg-base-100 shadow-sm border border-gray-200">
                    <div class="card-body p-4 h-40 flex flex-col items-center justify-center text-center">
                         <h3 class="w-full text-left text-xs font-bold text-gray-500 absolute top-4 left-4">PO On Process</h3>
                         <div class="text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            <span class="text-xs">There was a problem displaying this chart.</span>
                         </div>
                    </div>
                </div>
                 <!-- Card 3 -->
                  <div class="card bg-base-100 shadow-sm border border-gray-200">
                    <div class="card-body p-4 h-40 flex flex-col items-center justify-center text-center">
                         <h3 class="w-full text-left text-xs font-bold text-gray-500 absolute top-4 left-4">PO Accepted at Warehouse</h3>
                         <div class="text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            <span class="text-xs">There was a problem displaying this chart.</span>
                         </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                 <!-- Card 4 -->
                 <div class="card bg-base-100 shadow-sm border border-gray-200 h-64">
                    <div class="card-body p-4 flex flex-col items-center justify-end text-center">
                         <h3 class="w-full text-left text-xs font-bold text-gray-500 absolute top-4 left-4">Incoming PO - Details</h3>
                         <div class="text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                         </div>
                    </div>
                </div>
                 <!-- Card 5 -->
                 <div class="card bg-base-100 shadow-sm border border-gray-200 h-64">
                    <div class="card-body p-4 flex flex-col items-center justify-end text-center">
                         <h3 class="w-full text-left text-xs font-bold text-gray-500 absolute top-4 left-4">PO On Process - Details</h3>
                        <div class="text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                         </div>
                    </div>
                </div>
                 <!-- Card 6 -->
                 <div class="card bg-base-100 shadow-sm border border-gray-200 h-64">
                    <div class="card-body p-4 flex flex-col items-center justify-end text-center">
                         <h3 class="w-full text-left text-xs font-bold text-gray-500 absolute top-4 left-4">PO Accepted at Warehouse - Details</h3>
                         <div class="text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                         </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</body>
</html>
