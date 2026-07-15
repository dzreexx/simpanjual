@extends('layouts.app')

@section('title', 'Home Page')

@section('content')

    <div class="p-4">
        <form action="{{route('addproduct')}}" method="POST">
            @csrf
            <input type="text" placeholder="Product Name" name="product_name" id="product_name" class="input" />
            <select class="select appearance-none" name="id_brand" id="id_brand">
                <option disabled selected>Select Brand</option>
                @foreach ($brands as $brand)
                    <option value="{{$brand->id_brand}}">{{$brand->brand_name}}</option>
                @endforeach
            </select>
            <input type="number" placeholder="Price" name="price" id="price" class="input" />
            <!-- <input type="number" placeholder="Stock" name="stock" id="stock" class="input" /> -->
            <button class="btn" type="submit">Submit</button>
        </form>
    </div>

@endsection