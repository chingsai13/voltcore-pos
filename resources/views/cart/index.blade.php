@extends('layouts.voltcore')

@section('title', 'My Cart — Voltcore POS')
@section('header', 'Shopping Cart')

@section('content')
<div class="cols">
    <div class="panel wrap">
        <h2>Your Selected Hardware</h2>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                <tr>
                    <td style="font-weight: 600;">{{ $item->product->name }}</td>
                    <td class="mono">₱{{ number_format($item->product->price, 2) }}</td>
                    <td class="n">{{ $item->quantity }}</td>
                    <td class="mono" style="color: var(--gr);">₱{{ number_format($item->product->price * $item->quantity, 2) }}</td>
                    <td>
                        <form action="{{ route('cart.destroy', $item) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="chip" style="padding: 2px 8px; color: var(--err);">Remove</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--dim); padding: 24px;">Your cart is empty. <a href="{{ route('products.catalog') }}" style="color: var(--cy);">Browse catalog</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($items->isNotEmpty())
    <form class="panel" method="POST" action="{{ route('cart.checkout') }}">
        @csrf
        <h2>Checkout to Store</h2>
        <p style="color: var(--dim); font-size: 13px; margin-bottom: 16px;">
            Choose your preferred simulated payment method. Upon checkout, your order is dispatched live to the Staff POS Register.
        </p>

        <label for="payment_method">Payment Option *</label>
        <select name="payment_method" id="payment_method" required>
            <option value="Cash">Cash at Counter</option>
            <option value="GCash">GCash / QR E-Wallet</option>
            <option value="Credit Card">Credit / Debit Card</option>
            <option value="Bank Transfer">Online Bank Transfer</option>
        </select>

        <div style="margin-top: 18px;">
            <div class="lbl">Cart Total:</div>
            <div class="total">₱{{ number_format($total, 2) }}</div>
        </div>

        <button type="submit" class="btn" style="width: 100%; margin-top: 12px;">Checkout to Counter</button>
    </form>
    @endif
</div>
@endsection
