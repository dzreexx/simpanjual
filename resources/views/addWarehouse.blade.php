@extends('layouts.app')

@section('title', 'Add Warehouse')

@section('content')
    <div class="p-4">
        <form action="{{ route('addwarehouse') }}" method="POST">
            @csrf
            <input
                type="text"
                placeholder="Warehouse Name"
                name="warehouse_name"
                id="warehouse_name"
                class="input"
            />
            {{-- <input type="text" name="brand_name" id="brand_name"> --}}
            {{-- <button type="submit">Submit</button> --}}
            <button class="btn" type="submit">Submit</button>
        </form>
    </div>
@endsection
