<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Öğrenci Girişi</title>
</head>
<body>
<h2>Öğrenci Giriş</h2>

@if ($errors->any())
    <div>
        <strong>{{ $errors->first() }}</strong>
    </div>
@endif

<form method="POST" action="{{ route('ogrenci.login.submit') }}">
    @csrf
    <div>
        <label for="email">E-posta:</label>
        <input type="email" name="email" id="email" required autofocus>
    </div>

    <div>
        <label for="password">Şifre:</label>
        <input type="password" name="password" id="password" required>
    </div>

    <div>
        <label>
            <input type="checkbox" name="remember"> Beni Hatırla
        </label>
    </div>

    <button type="submit">Giriş Yap</button>
</form>
</body>
</html>
