@extends('layouts.app')

@section('title', 'Home Page')

@section('content')

<div class="p-4">
    <form action="{{route('addbrand')}}" method="POST">
        @csrf
        <input type="text" placeholder="Brand Name" name="brand_name" id="brand_name" class="input" />
        {{-- <input type="text" name="brand_name" id="brand_name"> --}}
        {{-- <button type="submit">Submit</button> --}}
        <button class="btn" type="submit">Submit</button>
    </form>
</div>

@endsection