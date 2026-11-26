<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register — Voltcore POS</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&family=Comfortaa:wght@700&family=Share+Tech+Mono&display=swap">
<link rel="stylesheet" href="{{ asset('css/voltcore.css') }}">
<style>
.gate{position:fixed;inset:0;display:grid;place-items:center;padding:20px;z-index:5;overflow-y:auto}
.gate .panel{width:min(480px,100%);--c:22px;padding:32px;margin:auto}
</style>
</head>
<body>
<div class="bg"></div>

<div class="gate">
  <form class="panel" method="POST" action="{{ route('register') }}" enctype="multipart/form-data" autocomplete="off">
    @csrf
    <div class="brand">VOLTCORE</div>
    <div class="sub">Create Account</div>

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-top: 14px;">
            <ul style="margin-left: 16px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <label for="username">Username *</label>
    <input id="username" type="text" name="username" value="{{ old('username') }}" placeholder="e.g. mikasantos" required autofocus>

    <label for="full_name">Full Name *</label>
    <input id="full_name" type="text" name="full_name" value="{{ old('full_name') }}" placeholder="e.g. Mika Santos" required>

    <label for="email">Email Address *</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="e.g. Mika@mail.com" required>

    <label for="password">Password *</label>
    <input id="password" type="password" name="password" placeholder="••••••••" required>

    <label for="password_confirmation">Confirm Password *</label>
    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="••••••••" required>

    <label for="avatar">Profile Photo / Avatar (Optional, Max 2MB)</label>
    <input id="avatar" type="file" name="avatar" accept="image/*">

    <div style="margin-top: 24px; display: flex; flex-direction: column; gap: 10px;">
      <button type="submit" class="btn" style="width:100%">Create Account</button>
      <div style="display: flex; justify-content: space-between; font-size: 13px; margin-top: 8px;">
        <a href="{{ route('login') }}" style="color: var(--cy); text-decoration: none;">Already registered? Sign in</a>
        <a href="{{ route('welcome') }}" style="color: var(--dim); text-decoration: none;">Back to Catalog</a>
      </div>
    </div>
  </form>
</div>
</body>
</html>
