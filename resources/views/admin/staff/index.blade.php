@extends('layouts.voltcore')

@section('title', 'Manage Staff — Voltcore POS')
@section('header', 'Staff & User Accounts')

@section('content')
<div style="display: flex; justify-content: flex-end; margin-bottom: 16px;">
    <a href="{{ route('admin.staff.create') }}" class="btn">+ Add Staff Member</a>
</div>

<div class="panel wrap">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Avatar</th>
                <th>Username</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($staff as $u)
            <tr>
                <td class="n">#{{ $u->id }}</td>
                <td>
                    @if ($u->avatar)
                        <img src="{{ asset('storage/' . $u->avatar) }}" alt="{{ $u->username }}" class="avatar-thumb">
                    @else
                        <div class="avatar-thumb" style="display: flex; align-items: center; justify-content: center; background: rgba(26, 217, 255, 0.2); color: var(--cy); font-weight: bold;">
                            {{ strtoupper(substr($u->username, 0, 1)) }}
                        </div>
                    @endif
                </td>
                <td class="mono" style="color: var(--cy);">{{ $u->username }}</td>
                <td style="font-weight: 600;">{{ $u->full_name }}</td>
                <td>{{ $u->email }}</td>
                <td><span class="chip" style="padding: 2px 8px; font-size: 11px;">{{ strtoupper($u->role) }}</span></td>
                <td style="display: flex; gap: 8px;">
                    <a href="{{ route('admin.staff.edit', $u) }}" class="chip" style="padding: 4px 10px;">Edit</a>
                    @if ($u->id !== Auth::id())
                    <form action="{{ route('admin.staff.destroy', $u) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this staff member?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="chip" style="padding: 4px 10px; color: var(--err);">Delete</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
