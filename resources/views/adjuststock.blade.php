@extends('layouts.app')

@section('title', 'Adjust Stock')

@section('content')

    <div class="p-4">
        <form action="{{route('adjuststock')}}" method="POST">
            @csrf
            <select class="select appearance-none" name="id_product" id="id_product">
                <option disabled selected>Select product</option>
                @foreach ($products as $product)
                    <option value="{{$product->id_product}}">{{$product->product_name}}</option>
                @endforeach
            </select>
            <select class="select appearance-none" name="id_warehouse" id="id_warehouse">
                <option disabled selected>Select warehouse</option>
                @foreach ($warehouses as $warehouse)
                    <option value="{{$warehouse->id_warehouse}}">{{$warehouse->warehouse_name}}</option>
                @endforeach
            </select>
            <input type="number" placeholder="Stock" name="stock" id="stock" class="input" />
            <button class="btn" type="submit">Submit</button>
        </form>
    </div>

@endsection