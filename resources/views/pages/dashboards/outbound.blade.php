@extends('layouts.app')

@section('title', 'Inbound')
@section('content')
    <div class="p-4 space-y-1 bg-white">
        {{-- <small>Relative Date</small> --}}
        <div class="flex space-x-1">
            <form class="filter">
                <input
                    class="btn btn-square border-gray-200 rounded-lg"
                    type="reset"
                    value="×"
                />
                <input
                    class="btn border-gray-200 rounded-lg"
                    type="radio"
                    name="frameworks"
                    aria-label="Today"
                />
                <input
                    class="btn border-gray-200 rounded-lg"
                    type="radio"
                    name="frameworks"
                    aria-label="Yesterday"
                />
                <input
                    class="btn border-gray-200 rounded-lg"
                    type="radio"
                    name="frameworks"
                    aria-label="Previous Week"
                />
                <input
                    class="btn border-gray-200 rounded-lg"
                    type="radio"
                    name="frameworks"
                    aria-label="Previous Month"
                />
            </form>
            <select
                class="select appearance-none border-2 border-gray-200 rounded-lg"
            >
                <option disabled selected>Warehouse</option>
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
                        Overall Orders - Brand
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
                        Total Order in Process - Brand
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
                        Total Order On Delivery - Brand
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
                        All Orders - Brand
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
                        Order in Process - Brand
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
                        Orders on Delivery - Brand
                    </span>
                    <div class="flex justify-center items-center h-full">
                        <h2 class="text-3xl font-bold">No data</h2>
                        {{-- <span class="text-xl">$29/mo</span> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="flex space-x-1 h-[35vh]">
            <div
                class="card w-[40%] h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
                <div class="card-body">
                    <span class="badge badge-lg badge-warning">
                        Total Pending Orders
                    </span>
                    <div class="flex justify-center items-center h-full">
                        <h2 class="text-3xl font-bold">No data</h2>
                        {{-- <span class="text-xl">$29/mo</span> --}}
                    </div>
                </div>
            </div>
            <div
                class="card w-[60%] h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
                <div class="card-body">
                    <span class="badge badge-lg badge-warning">
                        Pending Orders
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
