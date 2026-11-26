@extends('layouts.voltcore')

@section('title', 'Edit Product — Voltcore POS')
@section('header', 'Edit Product')

@section('content')
<div class="panel" style="max-width: 600px;">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label for="name">Product Name *</label>
        <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required>

        <label for="category">Category *</label>
        <select name="category" id="category" required>
            @foreach (['CPU', 'GPU', 'Memory', 'Storage', 'Display', 'Peripherals'] as $cat)
                <option value="{{ $cat }}" {{ old('category', $product->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>

        <label for="price">Price (₱) *</label>
        <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $product->price) }}" required>

        <label for="stock_quantity">Stock Quantity *</label>
        <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" required>

        <label for="image">Product Image (Leave blank to keep current)</label>
        @if ($product->image)
            <div style="margin-bottom: 8px;">
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="max-height: 80px; object-fit: contain;">
            </div>
        @endif
        <input type="file" name="image" id="image" accept="image/*">

        <div style="display: flex; gap: 12px; margin-top: 20px;">
            <button type="submit" class="btn">Update Product</button>
            <a href="{{ route('admin.products.index') }}" class="chip">Cancel</a>
        </div>
    </form>
</div>
@endsection
