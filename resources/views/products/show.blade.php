@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Product Details</h1>
    <div class="card">
        <div class="card-body">
            <p><strong>Name:</strong> {{ $product->name }}</p>
            <p><strong>Description:</strong> {{ $product->description }}</p>
            <p><strong>Price:</strong> {{ $product->price }}</p>
            <p><strong>Image:</strong></p>
            @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}" width="200">
            @endif
        </div>
    </div>
    <a href="{{ route('products.index') }}" class="btn btn-secondary mt-3">Back</a>
</div>
@endsection