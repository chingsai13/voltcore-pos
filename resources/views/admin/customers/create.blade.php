@extends('layouts.voltcore')

@section('title', 'Add Customer — Voltcore POS')
@section('header', 'Add New Customer')

@section('content')
<div class="panel" style="max-width: 500px;">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.customers.store') }}" method="POST">
        @csrf

        <label for="full_name">Full Name *</label>
        <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" placeholder="e.g. Mika Santos" required>

        <label for="email">Email Address *</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="e.g. mika@mail.com" required>

        <label for="phone">Phone Number</label>
        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="e.g. 0917 555 0101">

        <div style="display: flex; gap: 12px; margin-top: 20px;">
            <button type="submit" class="btn">Save Customer</button>
            <a href="{{ route('admin.customers.index') }}" class="chip">Cancel</a>
        </div>
    </form>
</div>
@endsection
