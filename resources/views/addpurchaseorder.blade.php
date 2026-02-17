@extends('layouts.app')

@section('title', 'Add Warehouse')

@section('content')

    <div class="p-4">
        <form action="{{route('storepurchaseorder')}}" method="POST">
            @csrf
            <select class="select appearance-none" name="id_warehouse" id="id_warehouse">
                <option disabled selected>Select warehouse</option>
                @foreach ($warehouses as $warehouse)
                    <option value="{{$warehouse->id_warehouse}}">{{$warehouse->warehouse_name}}</option>
                @endforeach
            </select>
            <select class="select appearance-none" name="id_brand" id="id_brand">
                <option disabled selected>Select Brand</option>
                @foreach ($brands as $brand)
                    <option value="{{$brand->id_brand}}">{{$brand->brand_name}}</option>
                @endforeach
            </select>
            <select class="select appearance-none" name="id_product" id="id_product">
                <option disabled selected>Select product</option>
                @foreach ($products as $product)
                    <option value="{{$product->id_product}}">{{$product->product_name}}</option>
                @endforeach
            </select>
            <input type="number" placeholder="Quantity" name="stock" id="stock" class="input" />
            <button class="btn" type="submit">Submit</button>
        </form>
    </div>

@endsection