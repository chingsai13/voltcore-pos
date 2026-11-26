@extends('layouts.voltcore')

@section('title', 'Edit Staff — Voltcore POS')
@section('header', 'Edit Staff Member')

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

    <form action="{{ route('admin.staff.update', $staff) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label for="username">Username *</label>
        <input type="text" name="username" id="username" value="{{ old('username', $staff->username) }}" required>

        <label for="full_name">Full Name *</label>
        <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $staff->full_name) }}" required>

        <label for="email">Email Address *</label>
        <input type="email" name="email" id="email" value="{{ old('email', $staff->email) }}" required>

        <label for="password">Password (Leave blank to keep unchanged)</label>
        <input type="password" name="password" id="password" placeholder="••••••••">

        <label for="role">Role *</label>
        <select name="role" id="role" required>
            <option value="staff" {{ old('role', $staff->role) == 'staff' ? 'selected' : '' }}>Staff</option>
            <option value="admin" {{ old('role', $staff->role) == 'admin' ? 'selected' : '' }}>Admin</option>
        </select>

        <label for="avatar">Avatar Image</label>
        @if ($staff->avatar)
            <div style="margin-bottom: 8px;">
                <img src="{{ asset('storage/' . $staff->avatar) }}" alt="{{ $staff->username }}" class="avatar-thumb" style="width: 50px; height: 50px;">
            </div>
        @endif
        <input type="file" name="avatar" id="avatar" accept="image/*">

        <div style="display: flex; gap: 12px; margin-top: 20px;">
            <button type="submit" class="btn">Update Staff</button>
            <a href="{{ route('admin.staff.index') }}" class="chip">Cancel</a>
        </div>
    </form>
</div>
@endsection
