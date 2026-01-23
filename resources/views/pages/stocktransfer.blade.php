@extends('layouts.app')

@section('title', 'Stock Transfer')

@section('content')
    <div class="bg-base-100 p-6 h-full font-sans">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-sky-600 flex items-center gap-2">
                Stock Transfer
                <span
                    class="tooltip tooltip-right"
                    data-tip="Stock Transfer Information"
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
                    class="btn btn-outline btn-info btn-sm text-sky-600 hover:text-white"
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
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-3 h-3 ml-1"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                        />
                    </svg>
                </button>
                <button
                    class="btn btn-sm bg-gray-100 text-gray-400 border-none hover:bg-gray-200"
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
                            d="M12 4.5v15m7.5-7.5h-15"
                        />
                    </svg>
                    Create New
                </button>
            </div>
        </div>

        {{-- Status Tabs --}}
        <div class="mb-6 overflow-x-auto">
            <div
                class="flex space-x-6 text-sm text-gray-500 border-b border-gray-200 pb-2 min-w-max"
            >
                <a href="#" class="hover:text-gray-700 px-1">Action Needed</a>
                <a
                    href="#"
                    class="text-sky-600 font-semibold border-b-2 border-sky-600 pb-2 px-1"
                >
                    No Action Needed
                </a>
                <a href="#" class="hover:text-gray-700 px-1">Received</a>
            </div>
        </div>

        {{-- Toolbar --}}
        <div
            class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6"
        >
            <div class="flex items-center gap-3">
                <div class="dropdown">
                    <label
                        tabindex="0"
                        class="btn btn-sm btn-outline btn-ghost border-gray-300 font-normal normal-case"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-4 h-4 mr-2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"
                            />
                        </svg>
                        10 row
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-3 h-3 ml-2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                            />
                        </svg>
                    </label>
                </div>
                <div class="dropdown">
                    <label
                        tabindex="0"
                        class="btn btn-sm btn-outline btn-ghost border-gray-300 font-normal normal-case"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-4 h-4 mr-2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5"
                            />
                        </svg>
                        Medium View
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-3 h-3 ml-2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                            />
                        </svg>
                    </label>
                </div>
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="relative w-full md:w-auto lg:w-[400px]">
                    <input
                        type="text"
                        placeholder="Search by TR Number, Item Name, Item SKU, Shipping Ser..."
                        class="input input-sm input-bordered w-full pr-10 rounded-full"
                    />
                    <div
                        class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"
                    >
                        <svg
                            aria-hidden="true"
                            class="w-4 h-4 text-gray-500"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            ></path>
                        </svg>
                    </div>
                </div>
                <button
                    class="btn btn-sm btn-outline border-gray-300 font-normal normal-case"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-4 h-4 mr-2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"
                        />
                    </svg>
                    All Filter
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div
            class="border border-base-200 rounded-lg bg-white min-h-[500px] flex flex-col relative"
        >
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="bg-white">
                                <label>
                                    <input
                                        type="checkbox"
                                        class="checkbox checkbox-sm rounded-sm"
                                    />
                                </label>
                            </th>
                            <th
                                class="bg-white text-gray-500 font-medium capitalize"
                            >
                                <div
                                    class="flex items-center gap-1 cursor-pointer hover:text-gray-700"
                                >
                                    TR Number
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="w-3 h-3"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5"
                                        />
                                    </svg>
                                </div>
                            </th>
                            <th
                                class="bg-white text-gray-500 font-medium capitalize"
                            >
                                <div
                                    class="flex items-center gap-1 cursor-pointer hover:text-gray-700"
                                >
                                    Status
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="w-3 h-3"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5"
                                        />
                                    </svg>
                                </div>
                            </th>
                            <th
                                class="bg-white text-gray-500 font-medium capitalize"
                            >
                                Source Location
                            </th>
                            <th
                                class="bg-white text-gray-500 font-medium capitalize"
                            >
                                Destination Location
                            </th>
                            <th
                                class="bg-white text-gray-500 font-medium capitalize"
                            >
                                <div
                                    class="flex items-center gap-1 cursor-pointer hover:text-gray-700"
                                >
                                    Created Date
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="w-3 h-3"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5"
                                        />
                                    </svg>
                                </div>
                            </th>
                            <th
                                class="bg-white text-gray-500 font-medium capitalize"
                            >
                                Notes
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- No Data Row -->
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div
                class="flex-1 flex flex-col justify-center items-center text-center p-10 text-gray-500"
            >
                <div class="mb-4 text-gray-300">
                    {{-- Placeholder Icon for the 'Ghost' --}}
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="0.5"
                        stroke="currentColor"
                        class="w-32 h-32 mx-auto"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z"
                        />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-600">
                    Whoa, looks like this place is as empty as a ghost town!
                </h3>
                <p class="text-sm mt-1">
                    Time to break the silence - hit
                    <span class="text-sky-500 font-medium">"Create New"</span>
                    and make your first stock transfer!
                </p>
            </div>
        </div>

        {{-- Footer/Pagination --}}
        <div
            class="flex justify-between items-center mt-4 text-xs text-gray-500"
        >
            <div>Showing 0-0 of 0 data.</div>
            <div class="btn-group">
                <button
                    class="btn btn-sm btn-ghost hover:bg-transparent text-gray-400"
                    disabled
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
                            d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5"
                        />
                    </svg>
                </button>
                <button
                    class="btn btn-sm btn-ghost hover:bg-transparent text-gray-400"
                    disabled
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
                </button>
                <button
                    class="btn btn-sm btn-outline border-sky-200 text-sky-500 bg-sky-50 hover:bg-sky-100 hover:border-sky-300"
                >
                    1
                </button>
                <button
                    class="btn btn-sm btn-ghost hover:bg-transparent text-gray-400"
                    disabled
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
                </button>
                <button
                    class="btn btn-sm btn-ghost hover:bg-transparent text-gray-400"
                    disabled
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
                            d="M11.25 4.5l7.5 7.5-7.5 7.5m-6-15l7.5 7.5-7.5 7.5"
                        />
                    </svg>
                </button>
            </div>
        </div>
    </div>
@endsection
