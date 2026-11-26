@extends('layouts.voltcore')

@section('title', 'Manage Customers — Voltcore POS')
@section('header', 'Customers Management')

@section('content')
<div style="display: flex; justify-content: flex-end; margin-bottom: 16px;">
    <a href="{{ route('admin.customers.create') }}" class="btn">+ Add New Customer</a>
</div>

<div class="panel wrap">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($customers as $c)
            <tr>
                <td class="n">#{{ $c->id }}</td>
                <td style="font-weight: 600;">{{ $c->full_name }}</td>
                <td>{{ $c->email }}</td>
                <td>{{ $c->phone ?: 'N/A' }}</td>
                <td style="display: flex; gap: 8px;">
                    <a href="{{ route('admin.customers.edit', $c) }}" class="chip" style="padding: 4px 10px;">Edit</a>
                    <form action="{{ route('admin.customers.destroy', $c) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this customer?');">
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
