@extends('layouts.voltcore')

@section('title', 'My Profile — Voltcore POS')
@section('header', 'User Profile')

@section('content')
<div class="panel" style="max-width: 500px;">
    <div style="display: flex; align-items: center; gap: 18px; margin-bottom: 20px;">
        @if ($user->avatar)
            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->username }}" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 2px solid var(--cy);">
        @else
            <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(26, 217, 255, 0.2); display: flex; align-items: center; justify-content: center; font-size: 26px; color: var(--cy); border: 2px solid var(--cy);">
                {{ strtoupper(substr($user->username, 0, 1)) }}
            </div>
        @endif
        <div>
            <h2 style="margin: 0;">{{ $user->full_name }}</h2>
            <span class="mono" style="color: var(--cy);">@<span>{{ $user->username }}</span></span>
            <span class="chip" style="margin-left: 8px; padding: 2px 8px; font-size: 11px;">{{ strtoupper($user->role) }}</span>
        </div>
    </div>

    <div class="row"><span class="lbl">Email</span><span>{{ $user->email }}</span></div>
    <div class="row"><span class="lbl">Account Type</span><span>{{ ucfirst($user->role) }}</span></div>
    <div class="row"><span class="lbl">Registered At</span><span class="mono">{{ $user->created_at->format('Y-m-d H:i') }}</span></div>

    @if ($user->isStaff())
    <div style="margin-top: 24px;">
        <a href="{{ route('admin.sales.create') }}" class="btn">Go to POS Register</a>
    </div>
    @endif
</div>
@endsection
