@extends('layouts.app')

@section('title', 'Inbound')
@section('content')
    <div class="p-4 space-y-1 bg-white h-full">
        {{-- <small>Relative Date</small> --}}
        <div class="flex space-x-1">
            <select
                class="select appearance-none border-2 border-gray-200 rounded-lg"
            >
                <option disabled selected>Date</option>
                <option>Crimson</option>
                <option>Amber</option>
                <option>Velvet</option>
            </select>
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
            <select
                class="select appearance-none border-2 border-gray-200 rounded-lg"
            >
                <option disabled selected>Order Number</option>
                <option>Crimson</option>
                <option>Amber</option>
                <option>Velvet</option>
            </select>
        </div>

        {{-- Oreder Level --}}

        <div class="flex space-x-1 h-full">
            <div
                class="card w-full h-full bg-base-100 shadow-sm rounded-lg border-gray-200 border"
            >
                <div class="card-body">
                    <span class="badge badge-lg badge-warning">
                        Stock Ledger
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
