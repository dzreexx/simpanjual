{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @vite('resources/css/app.css')
</head>
<body>
<div class="hover-3d">
  <!-- content -->
  <figure class="max-w-100 rounded-2xl">
    <img src="https://img.daisyui.com/images/stock/creditcard.webp" alt="3D card" />
  </figure>
  <!-- 8 empty divs needed for the 3D effect -->
  <div></div>
  <div></div>
  <div></div>
  <div></div>
  <div></div>
  <div></div>
  <div></div>
  <div></div>
</div>
</body>
</html> --}}

@extends('layouts.app')

@section('title', 'Home Page')

@section('content')
       <!-- Main Content -->
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
@endsection