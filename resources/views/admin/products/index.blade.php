@extends('layouts.voltcore')

@section('title', 'Manage Products — Voltcore POS')
@section('header', 'Products Management')

@section('content')
<div style="display: flex; justify-content: flex-end; margin-bottom: 16px;">
    <a href="{{ route('admin.products.create') }}" class="btn">+ Add New Product</a>
</div>

<div class="panel wrap">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $p)
            <tr>
                <td class="n">#{{ $p->id }}</td>
                <td>
                    @if ($p->image)
                        <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}" style="width: 40px; height: 40px; object-fit: contain; border-radius: 4px;">
                    @else
                        <span style="color: var(--dim); font-size: 12px;">None</span>
                    @endif
                </td>
                <td style="font-weight: 600;">{{ $p->name }}</td>
                <td><span class="chip" style="padding: 2px 8px; font-size: 11px;">{{ $p->category }}</span></td>
                <td class="n">₱{{ number_format($p->price, 2) }}</td>
                <td class="n" style="{{ $p->stock_quantity <= 3 ? 'color: var(--err);' : '' }}">{{ $p->stock_quantity }}</td>
                <td style="display: flex; gap: 8px;">
                    <a href="{{ route('admin.products.edit', $p) }}" class="chip" style="padding: 4px 10px;">Edit</a>
                    <form action="{{ route('admin.products.destroy', $p) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="chip" style="padding: 4px 10px; color: var(--err);">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
