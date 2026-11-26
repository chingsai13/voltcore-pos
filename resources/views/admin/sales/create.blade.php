@extends('layouts.voltcore')

@section('title', 'POS Register – Voltcore POS')
@section('header', 'POS Register')

@section('content')

{{-- ═══════════════════════════════════════════════════════
     PENDING ONLINE CUSTOMER ORDERS
══════════════════════════════════════════════════════════ --}}
@if ($pendingOrders->isNotEmpty())
<div class="panel" style="margin-bottom: 28px; border-color: var(--cy);">
    <h2 style="display:flex;align-items:center;gap:10px;">
        <span class="led"></span>
        Online Orders Awaiting Fulfilment
        <span class="chip" style="padding:2px 10px;font-size:12px;color:var(--cy);">
            {{ $pendingOrders->count() }} order{{ $pendingOrders->count() > 1 ? 's' : '' }}
        </span>
    </h2>
    <p style="color:var(--dim);font-size:13px;margin-bottom:20px;">
        Click <strong style="color:var(--cy);">Fulfill Entire Order</strong> to process all items, deduct stock, and record each sale at once.
    </p>

    @foreach ($pendingOrders as $userId => $orderItems)
    @php
        $buyer      = $orderItems->first()->user;
        $payMethod  = $orderItems->first()->payment_method ?? 'Cash';
        $orderTotal = $orderItems->sum(fn($i) => $i->product->price * $i->quantity);
    @endphp
    <div style="border:1px solid rgba(0,255,255,0.2);border-radius:10px;margin-bottom:16px;overflow:hidden;">
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;padding:12px 18px;background:rgba(0,255,255,0.05);border-bottom:1px solid rgba(0,255,255,0.15);">
            <div>
                <div style="font-weight:700;color:var(--cy);font-size:15px;">
                    {{ $buyer->full_name }}
                    <span style="font-weight:400;color:var(--dim);font-size:13px;">({{ $buyer->username }})</span>
                </div>
                <div style="font-size:12px;color:var(--dim);">{{ $buyer->email }}</div>
            </div>
            <div style="margin-left:auto;display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <span class="chip" style="padding:3px 10px;font-size:12px;">{{ $payMethod }}</span>
                <div style="font-family:'Share Tech Mono',monospace;font-size:20px;color:var(--gr);font-weight:700;">
                    ₱{{ number_format($orderTotal, 2) }}
                </div>
                <form method="POST" action="{{ route('admin.sales.fulfill') }}"
                    onsubmit="return confirm('Fulfill all {{ $orderItems->count() }} item(s) for {{ addslashes($buyer->full_name) }}?')">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $userId }}">
                    <button type="submit" class="btn" style="padding:7px 18px;font-size:13px;">
                        ✓ Fulfill Entire Order ({{ $orderItems->count() }})
                    </button>
                </form>
            </div>
        </div>
        <div class="wrap">
            <table>
                <thead><tr><th>Product</th><th>Category</th><th style="text-align:right;">Unit Price</th><th style="text-align:center;">Qty</th><th style="text-align:right;">Subtotal</th></tr></thead>
                <tbody>
                    @foreach ($orderItems as $item)
                    <tr>
                        <td style="font-weight:600;">{{ $item->product->name }}</td>
                        <td style="color:var(--dim);font-size:13px;">{{ $item->product->category }}</td>
                        <td class="mono" style="text-align:right;">₱{{ number_format($item->product->price, 2) }}</td>
                        <td class="n" style="text-align:center;">{{ $item->quantity }}</td>
                        <td class="mono" style="text-align:right;color:var(--gr);font-weight:600;">₱{{ number_format($item->product->price * $item->quantity, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="border-top:1px solid rgba(0,255,255,0.2);">
                        <td colspan="4" style="text-align:right;color:var(--dim);font-size:13px;padding:8px 12px;">Order Total</td>
                        <td class="mono" style="text-align:right;color:var(--cy);font-weight:700;font-size:16px;padding:8px 12px;">₱{{ number_format($orderTotal, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ═══════════════════════════════════════════════════════
     STAFF MULTI-ITEM REGISTER
══════════════════════════════════════════════════════════ --}}
<div class="cols" style="align-items: flex-start;">

    {{-- LEFT: Product picker --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Add item form --}}
        <form class="panel" method="POST" action="{{ route('admin.sales.register.add') }}" id="picker-form">
            @csrf
            <h2>Add Item to Register</h2>
            <p style="color:var(--dim);font-size:13px;margin-bottom:14px;">Pick a product and quantity, then click Add. Repeat for every item in the sale.</p>

            <label for="product_id">Product *</label>
            <select name="product_id" id="product_id" required onchange="updatePicker()">
                <option value="">— Select a product —</option>
                @foreach ($allProducts as $p)
                <option value="{{ $p->id }}"
                    data-price="{{ $p->price }}"
                    data-stock="{{ $p->stock_quantity }}"
                    data-cat="{{ $p->category }}">
                    {{ $p->name }} — ₱{{ number_format($p->price,2) }} ({{ $p->stock_quantity }} left)
                </option>
                @endforeach
            </select>

            <label for="qty">Quantity *</label>
            <input type="number" name="quantity" id="qty" min="1" value="1" required oninput="updatePicker()">

            {{-- Live preview --}}
            <div id="picker-preview" style="margin-top:12px;padding:12px;background:rgba(0,255,255,0.04);border:1px solid rgba(0,255,255,0.12);border-radius:8px;display:none;">
                <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--dim);">
                    <span id="pk-cat"></span>
                    <span id="pk-stock"></span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-top:6px;">
                    <span id="pk-name" style="font-weight:600;"></span>
                    <span id="pk-line" style="font-family:'Share Tech Mono',monospace;color:var(--cy);font-weight:700;"></span>
                </div>
            </div>

            <button type="submit" class="btn" style="width:100%;margin-top:14px;">+ Add to Register</button>
        </form>

        {{-- Customer & payment (applies to whole order) --}}
        <div class="panel">
            <h2>Order Details</h2>
            <p style="color:var(--dim);font-size:13px;margin-bottom:14px;">Set the customer and payment method for the entire order.</p>
            <label for="cust-sel">Customer (Optional)</label>
            <select id="cust-sel" form="confirm-form" name="customer_id">
                <option value="">Walk-in Customer</option>
                @foreach ($customers as $c)
                <option value="{{ $c->id }}">{{ $c->full_name }} ({{ $c->phone ?: $c->email }})</option>
                @endforeach
            </select>

            <label for="pay-sel" style="margin-top:12px;">Payment Method *</label>
            <select id="pay-sel" form="confirm-form" name="payment_method" required>
                <option value="Cash">Cash</option>
                <option value="GCash">GCash / QR E-Wallet</option>
                <option value="Credit Card">Credit / Debit Card</option>
                <option value="Bank Transfer">Online Bank Transfer</option>
            </select>
        </div>
    </div>

    {{-- RIGHT: Register cart --}}
    <div class="panel" style="flex:1.4;">
        <h2 style="display:flex;align-items:center;gap:10px;">
            Register Cart
            @if(count($register) > 0)
            <span class="chip" style="padding:2px 10px;font-size:12px;color:var(--cy);">{{ count($register) }} line{{ count($register) > 1 ? 's' : '' }}</span>
            @endif
        </h2>

        @if(count($register) > 0)
        <div class="wrap" style="margin-bottom:16px;">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th style="text-align:center;">Qty</th>
                        <th style="text-align:right;">Unit</th>
                        <th style="text-align:right;">Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @php $registerTotal = 0; @endphp
                    @foreach ($register as $line)
                    @php $registerTotal += $line['line_total']; @endphp
                    <tr>
                        <td>
                            <div style="font-weight:600;">{{ $line['product']->name }}</div>
                            <div style="font-size:11px;color:var(--dim);">{{ $line['product']->category }}</div>
                        </td>
                        <td style="text-align:center;" class="n">{{ $line['quantity'] }}</td>
                        <td class="mono" style="text-align:right;">₱{{ number_format($line['product']->price, 2) }}</td>
                        <td class="mono" style="text-align:right;color:var(--gr);font-weight:600;">₱{{ number_format($line['line_total'], 2) }}</td>
                        <td style="text-align:center;">
                            <form method="POST" action="{{ route('admin.sales.register.remove', $line['index']) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background:none;border:none;color:var(--err);cursor:pointer;font-size:16px;padding:4px 8px;" title="Remove">✕</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="border-top:1px solid rgba(0,255,255,0.2);">
                        <td colspan="3" style="text-align:right;color:var(--dim);font-size:13px;padding:10px 12px;">Order Total</td>
                        <td class="mono" colspan="2" style="text-align:right;color:var(--cy);font-weight:700;font-size:22px;padding:10px 12px;">
                            ₱{{ number_format($registerTotal, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Confirm button --}}
        <form id="confirm-form" method="POST" action="{{ route('admin.sales.register.confirm') }}"
            onsubmit="return confirm('Confirm sale of {{ count($register) }} item(s)? This will deduct stock and record all sales.')">
            @csrf
            {{-- customer_id and payment_method injected here via form="confirm-form" on the selects above --}}
            <button type="submit" class="btn" style="width:100%;font-size:16px;padding:14px;">
                ✓ Confirm Entire Sale &amp; Deduct Stock
            </button>
        </form>

        <form method="POST" action="{{ route('admin.sales.register.clear') }}" style="margin-top:10px;"
            onsubmit="return confirm('Clear the entire register?')">
            @csrf
            @method('DELETE')
            <button type="submit" style="width:100%;background:none;border:1px solid var(--err);color:var(--err);border-radius:8px;padding:8px;cursor:pointer;font-family:inherit;font-size:13px;">
                Clear Register
            </button>
        </form>

        @else
        <div style="text-align:center;padding:40px 0;color:var(--dim);">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" style="margin-bottom:12px;opacity:0.3;"><path d="M3 4h3l3 11h10l2-8H7"/><circle cx="10" cy="19" r="1.5"/><circle cx="18" cy="19" r="1.5"/></svg>
            <div>Register is empty.</div>
            <div style="font-size:13px;margin-top:4px;">Add products using the picker on the left.</div>
        </div>
        @endif
    </div>
</div>

<script>
function updatePicker() {
    const sel     = document.getElementById('product_id');
    const opt     = sel.options[sel.selectedIndex];
    const preview = document.getElementById('picker-preview');
    if (!opt || !opt.value) { preview.style.display = 'none'; return; }

    const price = parseFloat(opt.dataset.price) || 0;
    const stock = parseInt(opt.dataset.stock)   || 0;
    const qty   = parseInt(document.getElementById('qty').value) || 0;

    document.getElementById('pk-name').textContent  = opt.text.split(' —')[0];
    document.getElementById('pk-cat').textContent   = opt.dataset.cat;
    document.getElementById('pk-stock').textContent = stock + ' in stock';
    document.getElementById('pk-line').textContent  = '₱' + (price * qty).toLocaleString('en-PH', {minimumFractionDigits:2});
    preview.style.display = 'block';
}
updatePicker();
</script>
@endsection
