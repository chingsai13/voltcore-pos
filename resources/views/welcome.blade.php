@extends('layouts.voltcore')

@section('title', 'Welcome to Voltcore POS')
@section('header', 'Welcome to Voltcore')

@section('content')
{{-- Toast notification (shared style with catalog) --}}
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

<div class="panel" style="margin-bottom: 24px;">
    <h2>High Performance Hardware &amp; Custom Rigs</h2>
    <p style="color: var(--dim); margin-bottom: 16px;">
        Browse our catalog of genuine processors, GPUs, high-speed memory kits, and accessories.
        Sign in to add items to your cart, or visit the catalog below.
    </p>
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="{{ route('products.catalog') }}" class="btn">Explore Products</a>
        @guest
            <a href="{{ route('login') }}" class="chip">Staff / Customer Sign In</a>
            <a href="{{ route('register') }}" class="chip">New? Register</a>
        @else
            @if (Auth::user()->isStaff())
                <a href="{{ route('admin.sales.create') }}" class="chip">Open Register</a>
            @else
                <a href="{{ route('cart.index') }}" class="chip">My Cart
                    @php $cartCount = \App\Models\CartItem::where('user_id', auth()->id())->where('status','in_cart')->sum('quantity'); @endphp
                    @if($cartCount > 0)<span style="color:var(--cy);font-weight:700;"> ({{ $cartCount }})</span>@endif
                </a>
            @endif
        @endguest
    </div>
</div>

<h2>Featured Hardware</h2>
<div class="grid">
    @foreach ($products as $p)
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
    @endforeach
</div>

@auth
@if (!Auth::user()->isStaff())
<style>
.atc-btn { position: relative; overflow: hidden; transition: all 0.2s; }
.atc-btn.loading { opacity: 0.7; cursor: wait; }
.atc-btn.success-state {
    background: linear-gradient(135deg, #00ff88 0%, #00ccaa 100%) !important;
    border-color: #00ff88 !important; color: #000 !important;
    transform: scale(1.04);
}
.atc-btn.error-state {
    background: linear-gradient(135deg, #ff3366 0%, #cc0033 100%) !important;
    border-color: #ff3366 !important; color: #fff !important;
}
.atc-ripple {
    position: absolute; border-radius: 50%;
    background: rgba(255,255,255,0.35);
    transform: scale(0); animation: atc-ripple-anim 0.55s linear; pointer-events: none;
}
@keyframes atc-ripple-anim { to { transform: scale(4); opacity: 0; } }
#vc-toast.show { transform: translateY(0); opacity: 1; }
#vc-toast.toast-ok  { border-color: #00ff88; box-shadow: 0 0 24px rgba(0,255,136,0.25); }
#vc-toast.toast-err { border-color: #ff3366; box-shadow: 0 0 24px rgba(255,51,102,0.25); }
</style>

<script>
const CART_URL = "{{ route('cart.store') }}";
const CSRF    = "{{ csrf_token() }}";

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

function updateCartBadge(count) {
    const badge = document.getElementById('cart-badge');
    if (!badge) return;
    badge.textContent = count;
    badge.style.display = count > 0 ? 'inline' : 'none';
}

function spawnRipple(btn, e) {
    const r = document.createElement('span');
    r.className = 'atc-ripple';
    const rect = btn.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    r.style.cssText = `width:${size}px;height:${size}px;left:${e.clientX-rect.left-size/2}px;top:${e.clientY-rect.top-size/2}px`;
    btn.appendChild(r);
    r.addEventListener('animationend', () => r.remove());
}

document.querySelectorAll('.atc-btn').forEach(btn => {
    btn.addEventListener('click', async function(e) {
        if (this.classList.contains('loading')) return;
        spawnRipple(this, e);
        const label = this.querySelector('.atc-label');
        const original = label.textContent;
        this.classList.add('loading');
        label.textContent = 'Adding…';
        try {
            const res = await fetch(CART_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ product_id: this.dataset.productId, quantity: 1 }),
            });
            const data = await res.json();
            if (res.ok && data.success) {
                this.classList.remove('loading');
                this.classList.add('success-state');
                label.textContent = '✓ Added!';
                showToast(data.message, true);
                updateCartBadge(data.cart_count);
                setTimeout(() => { this.classList.remove('success-state'); label.textContent = original; }, 1800);
            } else {
                throw new Error(data.message || 'Could not add item.');
            }
        } catch (err) {
            this.classList.remove('loading');
            this.classList.add('error-state');
            label.textContent = '✗ Failed';
            showToast(err.message || 'Something went wrong.', false);
            setTimeout(() => { this.classList.remove('error-state'); label.textContent = original; }, 1800);
        }
    });
});
</script>
@endif
@endauth
@endsection
