@extends('layouts.app')

@section('title', 'Add Warehouse')

@section('content')
    <div class="p-4">
        @if (session('success'))
            <h1>{{ session('success') }}</h1>
        @endif

        @if (session('error'))
            <h1>{{ session('error') }}</h1>
        @endif

        <form action="{{ route('storesalesorder') }}" method="POST">
            @csrf
            <fieldset class="fieldset">
                <input
                    type="text"
                    placeholder="Customer Name"
                    class="input input-neutral border border-neutral-500 rounded-md"
                    name="customer_name"
                    id="customer_name"
                    value="{{ old('customer_name') }}"
                />
                @error('customer_name')
                    <p class="label text-red-500">{{ $message }}</p>
                @enderror
            </fieldset>
            <fieldset class="fieldset">
                <input
                    type="number"
                    placeholder="Quantity"
                    class="input input-neutral border border-neutral-500 rounded-md"
                    name="quantity"
                    id="quantity"
                    value="{{ old('quantity') }}"
                />
                @error('quantity')
                    <p class="label text-red-500">{{ $message }}</p>
                @enderror
            </fieldset>
            <fieldset class="fieldset">
                <select
                    class="select appearance-none border border-neutral-500 rounded-md"
                    name="id_product"
                    id="id_product"
                >
                    <option disabled selected>Select product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id_product }}">
                            {{ $product->product_name }}
                        </option>
                    @endforeach
                </select>
                @error('id_product')
                    <p class="label text-red-500">{{ $message }}</p>
                @enderror
            </fieldset>
            <fieldset class="fieldset">
                <select
                    class="select appearance-none border border-neutral-500 rounded-md"
                    name="id_warehouse"
                    id="id_warehouse"
                >
                    <option disabled selected>Select warehouse</option>
                </select>
                @error('id_warehouse')
                    <p class="label text-red-500">{{ $message }}</p>
                @enderror
            </fieldset>
            <fieldset class="fieldset">
                <select
                    class="select appearance-none border border-neutral-500 rounded-md"
                    name="status"
                    id="status"
                >
                    <option disabled selected>Status</option>
                    <option value="unpaid">Unpaid</option>
                    <option value="new order">New Order</option>
                    <option value="hold">Hold</option>
                    <option value="ready to ship">ready to ship</option>
                    <option value="shipping">shipping</option>
                    <option value="completed">completed</option>
                    <option value="cancelled">cancelled</option>
                    <option value="missing data">missing data</option>
                    <option value="oversell">oversell</option>
                </select>
                @error('status')
                    <p class="label text-red-500">{{ $message }}</p>
                @enderror
            </fieldset>
            <button class="btn" type="submit">Submit</button>
        </form>
    </div>
    <script>
        document
            .getElementById('id_product')
            .addEventListener('change', function () {
                const id_product = this.value;
                const id_warehouse = document.getElementById('id_warehouse');
                console.log(id_product);

                id_warehouse.innerHTML =
                    '<option disabled selected>Select warehouse</option>';

                fetch('/getwarehouses/' + id_product)
                    .then((response) => response.json())
                    .then((data) => {
                        data.forEach((item) => {
                            const option = document.createElement('option');
                            option.value = item.id_warehouse;
                            option.text = item.warehouse.warehouse_name;
                            id_warehouse.appendChild(option);
                        });
                        console.log(data);
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            });
    </script>
@endsection
