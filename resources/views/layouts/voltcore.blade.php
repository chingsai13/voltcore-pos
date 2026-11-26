<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Voltcore POS')</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&family=Comfortaa:wght@700&family=Share+Tech+Mono&display=swap">
<link rel="stylesheet" href="{{ asset('css/voltcore.css') }}">
</head>
<body>
<div class="bg"></div>

<div class="app">
  <aside class="panel side">
    <div class="brand">VOLTCORE</div>
    <div class="sub">Point-of-Sale System</div>
    
    <nav class="nav">
      {{-- Public / Storefront links --}}
      <a href="{{ route('welcome') }}" class="{{ request()->routeIs('welcome') ? 'act' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>Home
      </a>
      <a href="{{ route('products.catalog') }}" class="{{ request()->routeIs('products.catalog') ? 'act' : '' }}">
        <svg viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16"/><path d="M9 9h6v6H9zM2 9h2M2 15h2M20 9h2M20 15h2"/></svg>Catalog
      </a>
      <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'act' : '' }}">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>About
      </a>

      {{-- Admin & Staff management links --}}
      @auth
        @if (Auth::user()->isStaff())
          <div style="margin: 12px 0 4px; padding-left: 12px; font-size: 11px; text-transform: uppercase; color: var(--cy); letter-spacing: 1px;">Management</div>
          <a href="{{ route('admin.sales.create') }}" class="{{ request()->routeIs('admin.sales.create') ? 'act' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M3 4h3l3 11h10l2-8H7"/><circle cx="10" cy="19" r="1.5"/><circle cx="18" cy="19" r="1.5"/></svg>Record Sale
          </a>
          <a href="{{ route('admin.sales.history') }}" class="{{ request()->routeIs('admin.sales.history') ? 'act' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M4 5h16M4 12h16M4 19h10"/></svg>Sales History
          </a>
          <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'act' : '' }}">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>Products CRUD
          </a>
          <a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.*') ? 'act' : '' }}">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>Customers
          </a>
          <a href="{{ route('admin.staff.index') }}" class="{{ request()->routeIs('admin.staff.*') ? 'act' : '' }}">
            <svg viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13"/><path d="M9 7V4h6v3"/></svg>Staff Team
          </a>
        @endif
        
        {{-- Cart link for customers only --}}
        @if (!Auth::user()->isStaff())
          @php $cartCount = \App\Models\CartItem::where('user_id', auth()->id())->where('status','in_cart')->sum('quantity'); @endphp
          <a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.*') ? 'act' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M3 4h3l3 11h10l2-8H7"/><circle cx="10" cy="19" r="1.5"/><circle cx="18" cy="19" r="1.5"/></svg>My Cart
            <span id="cart-badge" style="background:var(--cy);color:#000;border-radius:9px;padding:1px 7px;font-size:11px;margin-left:4px;font-weight:700;{{ $cartCount > 0 ? '' : 'display:none;' }}">{{ $cartCount }}</span>
          </a>
        @endif

        <a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') ? 'act' : '' }}">
          <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>Profile
        </a>
      @endauth
    </nav>

    <div class="me">
      <span class="led"></span>
      @auth
        <span style="font-weight: 600;">{{ Auth::user()->username }} ({{ ucfirst(Auth::user()->role) }})</span>
        <form action="{{ route('logout') }}" method="POST" style="margin-left: auto;">
          @csrf
          <button type="submit" style="background: none; border: none; color: var(--err); cursor: pointer; font: inherit; font-size: 12px;">Sign out</button>
        </form>
      @else
        <span>Guest</span>
        <a href="{{ route('login') }}" class="chip" style="margin-left: auto; padding: 3px 10px; font-size: 12px;">Login</a>
      @endauth
    </div>
  </aside>

  <main>
    <header class="top">
      <h1>@yield('header', 'Voltcore POS')</h1>
      <span class="mono" id="clk" style="color: var(--cy);"></span>
    </header>

    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @yield('content')
  </main>
</div>

<script>
const clk=()=>document.getElementById('clk').textContent=new Date().toLocaleTimeString('en-GB');clk();setInterval(clk,1000);
</script>
</body>
</html>
