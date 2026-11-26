@extends('layouts.voltcore')

@section('title', 'Product Catalog – Voltcore POS')
@section('header', 'Hardware Catalog')

@section('content')
{{-- Toast notification --}}
<div id="vc-toast" style="
    position: fixed; bottom: 28px; right: 28px; z-index: 9999;
    background: rgba(10,20,30,0.92); border: 1px solid var(--cy);
    border-radius: 10px; padding: 14px 22px; display: flex; align-items: center; gap: 12px;
    box-shadow: 0 0 24px rgba(0,255,255,0.2); backdrop-filter: blur(10px);
    transform: translateY(80px); opacity: 0; transition: all 0.35s cubic-bezier(.4,0,.2,1);
    pointer-events: none; min-width: 220px; max-width: 320px;
" aria-live="polite">
    <svg id="vc-toast-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"></svg>
    <span id="vc-toast-msg" style="font-size:14px; font-weight:600; color:#e0f8ff;"></span>
</div>

<form method="GET" action="{{ route('products.catalog') }}" class="bar2">
    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
        @foreach ($categories as $c)
            <a href="{{ route('products.catalog', ['category' => $c, 'sort' => $sort, 'q' => $search]) }}" class="chip {{ $category == $c ? 'act' : '' }}">{{ $c }}</a>
        @endforeach
    </div>

    <div style="display: flex; gap: 8px; margin-left: auto;">
        <select name="sort" onchange="this.form.submit()" style="max-width: 170px;">
            <option value="popularity" {{ $sort == 'popularity' ? 'selected' : '' }}>Popularity</option>
            <option value="price_low" {{ $sort == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
            <option value="price_high" {{ $sort == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
            <option value="name" {{ $sort == 'name' ? 'selected' : '' }}>Name A-Z</option>
        </select>
        <input type="hidden" name="category" value="{{ $category }}">
        <input type="text" name="q" value="{{ $search }}" placeholder="Search products..." style="max-width: 200px;">
    </div>
</form>

<div class="grid">
    @forelse ($products as $p)
    <article class="panel card">
        <div class="img">
            @if ($p->image)
                <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}" style="max-height: 100px; max-width: 100%; object-fit: contain;">
            @else
                <svg viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16"/><path d="M9 9h6v6H9zM2 9h2M2 15h2M20 9h2M20 15h2"/></svg>
            @endif
        </div>
        <div class="cat" style="color: var(--gr); font-size: 12px;">{{ $p->category }}</div>
        <div class="name" style="font-weight: 600;">{{ $p->name }}</div>
        <div class="price" style="color: var(--cy); font-family: 'Share Tech Mono', monospace; font-size: 20px;">₱{{ number_format($p->price, 2) }}</div>
        <div class="stock"><span>{{ $p->stock_quantity > 0 ? $p->stock_quantity . ' in stock' : 'Out of stock' }}</span></div>

        @auth
            @if (Auth::user()->isStaff())
                <a href="{{ route('admin.sales.create', ['product_id' => $p->id]) }}" class="btn" style="margin-top: 8px;">Record Sale</a>
            @elseif ($p->stock_quantity > 0)
                <button
                    type="button"
                    class="btn atc-btn"
                    style="width:100%; margin-top: 8px;"
                    data-product-id="{{ $p->id }}"
                    data-url="{{ route('cart.store') }}"
                    data-csrf="{{ csrf_token() }}"
                >
                    <span class="atc-label">Add to Cart</span>
                </button>
            @else
                <button class="btn" style="margin-top: 8px; opacity: 0.5; cursor: not-allowed;" disabled>Out of Stock</button>
            @endif
        @else
            <a href="{{ route('login') }}" class="btn" style="margin-top: 8px;">Sign in to Buy</a>
        @endauth
    </article>
    @empty
    <p style="color: var(--dim);">No products found matching your search criteria.</p>
    @endforelse
</div>

@auth
@if (!Auth::user()->isStaff())
<style>
/* Button states */
.atc-btn { position: relative; overflow: hidden; transition: all 0.2s; }
.atc-btn.loading { opacity: 0.7; cursor: wait; }
.atc-btn.success-state {
    background: linear-gradient(135deg, #00ff88 0%, #00ccaa 100%) !important;
    border-color: #00ff88 !important;
    color: #000 !important;
    transform: scale(1.04);
}
.atc-btn.error-state {
    background: linear-gradient(135deg, #ff3366 0%, #cc0033 100%) !important;
    border-color: #ff3366 !important;
    color: #fff !important;
}

/* Ripple */
.atc-ripple {
    position: absolute; border-radius: 50%;
    background: rgba(255,255,255,0.35);
    transform: scale(0);
    animation: atc-ripple-anim 0.55s linear;
    pointer-events: none;
}
@keyframes atc-ripple-anim {
    to { transform: scale(4); opacity: 0; }
}

/* Toast */
#vc-toast.show { transform: translateY(0); opacity: 1; }
#vc-toast.toast-ok  { border-color: #00ff88; box-shadow: 0 0 24px rgba(0,255,136,0.25); }
#vc-toast.toast-err { border-color: #ff3366; box-shadow: 0 0 24px rgba(255,51,102,0.25); }
</style>

<script>
const CART_URL = "{{ route('cart.store') }}";
const CSRF    = "{{ csrf_token() }}";

// --- Toast ---
let toastTimer = null;
function showToast(msg, ok = true) {
    const toast = document.getElementById('vc-toast');
    const icon  = document.getElementById('vc-toast-icon');
    const text  = document.getElementById('vc-toast-msg');

    toast.classList.remove('show','toast-ok','toast-err');

    text.textContent = msg;
    icon.innerHTML = ok
        ? '<polyline points="20 6 9 17 4 12" stroke="#00ff88"/>'
        : '<line x1="18" y1="6" x2="6" y2="18" stroke="#ff3366"/><line x1="6" y1="6" x2="18" y2="18" stroke="#ff3366"/>';

    toast.classList.add('show', ok ? 'toast-ok' : 'toast-err');

    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('show'), 2800);
}

// --- Update cart badge in sidebar ---
function updateCartBadge(count) {
    const badge = document.getElementById('cart-badge');
    if (!badge) return;
    if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'inline';
    } else {
        badge.style.display = 'none';
    }
}

// --- Ripple effect ---
function spawnRipple(btn, e) {
    const r = document.createElement('span');
    r.className = 'atc-ripple';
    const rect = btn.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    r.style.cssText = `width:${size}px;height:${size}px;left:${e.clientX-rect.left-size/2}px;top:${e.clientY-rect.top-size/2}px`;
    btn.appendChild(r);
    r.addEventListener('animationend', () => r.remove());
}

// --- Add to Cart handler ---
document.querySelectorAll('.atc-btn').forEach(btn => {
    btn.addEventListener('click', async function(e) {
        if (this.classList.contains('loading')) return;

        spawnRipple(this, e);

        const productId = this.dataset.productId;
        const label     = this.querySelector('.atc-label');
        const original  = label.textContent;

        this.classList.add('loading');
        label.textContent = 'Adding…';

        try {
            const res = await fetch(CART_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': CSRF,
                },
                body: JSON.stringify({ product_id: productId, quantity: 1 }),
            });

            const data = await res.json();

            if (res.ok && data.success) {
                // Success state
                this.classList.remove('loading');
                this.classList.add('success-state');
                label.textContent = '✓ Added!';
                showToast(data.message, true);
                updateCartBadge(data.cart_count);

                // Reset button after 1.8s
                setTimeout(() => {
                    this.classList.remove('success-state');
                    label.textContent = original;
                }, 1800);
            } else {
                throw new Error(data.message || 'Could not add item.');
            }
        } catch (err) {
            this.classList.remove('loading');
            this.classList.add('error-state');
            label.textContent = '✗ Failed';
            showToast(err.message || 'Something went wrong.', false);

            setTimeout(() => {
                this.classList.remove('error-state');
                label.textContent = original;
            }, 1800);
        }
    });
});
</script>
@endif
@endauth
@endsection
