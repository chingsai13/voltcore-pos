@extends('layouts.voltcore')

@section('title', 'About — Voltcore POS')
@section('header', 'About Voltcore POS')

@section('content')
<div class="panel" style="max-width: 650px;">
    <h2>Project Overview</h2>
    <p style="color: var(--dim); margin-bottom: 16px;">
        Voltcore POS is a cyberpunk-themed, full-stack Point-of-Sale application developed with Laravel 12 and MySQL.
        It features automated inventory decrement on sale, product image handling, staff management with avatars,
        and role-based security protecting all administrative actions.
    </p>

    <h2>Development Team</h2>
    <div class="row"><span class="lbl">Course</span><span>IT0049 — Web System Technologies</span></div>
    <div class="row"><span class="lbl">Developer Group</span><span>Benj Lorenz U. Regoso & Team</span></div>
    <div class="row"><span class="lbl">Architecture</span><span>Laravel 12 MVC + MySQL XAMPP</span></div>
    <div class="row"><span class="lbl">Authentication</span><span>Breeze + Session Auth + Middleware</span></div>
    <div class="row"><span class="lbl">Design System</span><span>Voltcore Chamfered Neon Liquid Glass</span></div>
</div>
@endsection
