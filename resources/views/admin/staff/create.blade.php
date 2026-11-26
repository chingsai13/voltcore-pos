@extends('layouts.voltcore')

@section('title', 'Add Staff — Voltcore POS')
@section('header', 'Add Staff Member')

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

    <form action="{{ route('admin.staff.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label for="username">Username *</label>
        <input type="text" name="username" id="username" value="{{ old('username') }}" placeholder="e.g. staff02" required>

        <label for="full_name">Full Name *</label>
        <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" placeholder="e.g. Juan Luna" required>

        <label for="email">Email Address *</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="e.g. juan@voltcore.com" required>

        <label for="password">Password *</label>
        <input type="password" name="password" id="password" required>

        <label for="role">Role *</label>
        <select name="role" id="role" required>
            <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
        </select>

        <label for="avatar">Avatar Image (Max 2MB)</label>
        <input type="file" name="avatar" id="avatar" accept="image/*">

        <div style="display: flex; gap: 12px; margin-top: 20px;">
            <button type="submit" class="btn">Save Staff</button>
            <a href="{{ route('admin.staff.index') }}" class="chip">Cancel</a>
        </div>
    </form>
</div>
@endsection
