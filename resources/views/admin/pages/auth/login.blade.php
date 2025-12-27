<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yetkili Giriş</title>
</head>
<body>
<h1>Yetkili Kullanıcı Girişi</h1>
<form method="POST" action="{{ route('auth.login.submit') }}">
    @csrf
    <div>
        <label>Email:</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus>
    </div>
    <div>
        <label>Şifre:</label>
        <input type="password" name="password" required>
    </div>
    <button type="submit">Giriş Yap</button>
</form>

@php dd(url()->previous()) @endphp
</body>
</html>
