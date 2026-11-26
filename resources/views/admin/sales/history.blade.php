@extends('layouts.voltcore')

@section('title', 'Sales History — Voltcore POS')
@section('header', 'Transaction History')

@section('content')
<div class="panel wrap">
    <table>
        <thead>
            <tr>
                <th>Date & Time</th>
                <th>Product</th>
                <th>Customer</th>
                <th>Staff</th>
                <th>Payment</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sales as $s)
            <tr>
                <td class="n">{{ $s->created_at->format('Y-m-d H:i') }}</td>
                <td style="font-weight: 600;">{{ $s->product->name ?? 'Deleted Product' }}</td>
                <td>{{ $s->customer ? $s->customer->full_name : 'Walk-in Customer' }}</td>
                <td style="color: var(--cy);">{{ $s->staff->username ?? 'N/A' }}</td>
                <td><span class="chip" style="padding: 2px 8px; font-size: 11px;">{{ $s->payment_method }}</span></td>
                <td class="n">{{ $s->quantity }}</td>
                <td class="n" style="color: var(--gr); font-weight: 600;">₱{{ number_format($s->total_price, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; color: var(--dim);">No sales recorded yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
