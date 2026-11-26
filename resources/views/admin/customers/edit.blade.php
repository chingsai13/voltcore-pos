@extends('layouts.voltcore')

@section('title', 'Edit Customer — Voltcore POS')
@section('header', 'Edit Customer Record')

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

    <form action="{{ route('admin.customers.update', $customer) }}" method="POST">
        @csrf
        @method('PUT')

        <label for="full_name">Full Name *</label>
        <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $customer->full_name) }}" required>

        <label for="email">Email Address *</label>
        <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}" required>

        <label for="phone">Phone Number</label>
        <input type="text" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}">

        <div style="display: flex; gap: 12px; margin-top: 20px;">
            <button type="submit" class="btn">Update Customer</button>
            <a href="{{ route('admin.customers.index') }}" class="chip">Cancel</a>
        </div>
    </form>
</div>
@endsection
