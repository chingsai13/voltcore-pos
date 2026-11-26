<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sign In – Voltcore POS</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&family=Comfortaa:wght@700&family=Share+Tech+Mono&display=swap">
<link rel="stylesheet" href="{{ asset('css/voltcore.css') }}">
<style>
.gate{position:fixed;inset:0;display:grid;place-items:center;padding:20px;z-index:5}
.gate .panel{width:min(420px,100%);--c:22px;padding:32px}
</style>
</head>
<body>
<div class="bg"></div>

<div class="gate">
  <form class="panel" method="POST" action="{{ route('login') }}" autocomplete="off">
    @csrf
    <div class="brand">VOLTCORE</div>
    <div class="sub">Portal Sign In (Staff, Admin, Customer)</div>

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-top: 14px;">
            <ul style="margin-left: 16px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <label for="email">Email Address</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="e.g. admin@voltcore.com" required autofocus>

    <label for="password">Password</label>
    <input id="password" type="password" name="password" placeholder="••••••••" required>

    <label style="display:flex;align-items:center;gap:8px;font-size:13px;margin-top:4px;">
        <input type="checkbox" name="remember" style="width:auto;accent-color:var(--cy);">
        Remember me
    </label>

    <div style="margin-top: 24px; display: flex; flex-direction: column; gap: 10px;">
      <button type="submit" class="btn" style="width:100%">Sign in</button>
      <div style="display: flex; justify-content: space-between; font-size: 13px; margin-top: 8px;">
        <a href="{{ route('register') }}" style="color: var(--cy); text-decoration: none;">New customer? Register</a>
        <a href="{{ route('welcome') }}" style="color: var(--dim); text-decoration: none;">Back to Catalog</a>
      </div>
    </div>

    <div style="margin-top: 20px; padding: 12px; background: rgba(0,255,255,0.05); border: 1px solid rgba(0,255,255,0.15); border-radius: 6px; font-size: 12px; color: var(--dim);">
      <strong style="color:var(--cy);">Test Accounts:</strong><br>
      admin@voltcore.com / password (Admin)<br>
      staff@voltcore.com / password (Staff)<br>
      customer@voltcore.com / password (Customer)
    </div>
  </form>
</div>
</body>
</html>
