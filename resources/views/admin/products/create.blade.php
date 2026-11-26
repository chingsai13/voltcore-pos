@extends('layouts.voltcore')

@section('title', 'Add Product — Voltcore POS')
@section('header', 'Add New Product')

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

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label for="name">Product Name *</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="e.g. RTX 5070 12GB Graphics Card" required>

        <label for="category">Category *</label>
        <select name="category" id="category" required>
            <option value="CPU" {{ old('category') == 'CPU' ? 'selected' : '' }}>CPU (Processors)</option>
            <option value="GPU" {{ old('category') == 'GPU' ? 'selected' : '' }}>GPU (Graphics Cards)</option>
            <option value="Memory" {{ old('category') == 'Memory' ? 'selected' : '' }}>Memory (RAM)</option>
            <option value="Storage" {{ old('category') == 'Storage' ? 'selected' : '' }}>Storage (SSD / NVMe)</option>
            <option value="Display" {{ old('category') == 'Display' ? 'selected' : '' }}>Display (Monitors)</option>
            <option value="Peripherals" {{ old('category') == 'Peripherals' ? 'selected' : '' }}>Peripherals (Keyboards/Mice)</option>
        </select>

        <label for="price">Price (₱) *</label>
        <input type="number" step="0.01" name="price" id="price" value="{{ old('price') }}" placeholder="0.00" required>

        <label for="stock_quantity">Stock Quantity *</label>
        <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', 1) }}" min="0" required>

        <label for="image">Product Image (Max 2MB)</label>
        <input type="file" name="image" id="image" accept="image/*">

        <div style="display: flex; gap: 12px; margin-top: 20px;">
            <button type="submit" class="btn">Save Product</button>
            <a href="{{ route('admin.products.index') }}" class="chip">Cancel</a>
        </div>
    </form>
</div>
@endsection
