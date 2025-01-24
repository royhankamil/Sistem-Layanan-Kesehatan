<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h2>Form Register</h2>
    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif
    <form action="{{ route('register') }}" method="POST">
        @csrf
        <label for="name">Nama:</label>
        <input type="text" name="name" required>
        <br>

        <label for="email">Email:</label>
        <input type="email" name="email" required>
        <br>

        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <br>

        <label for="password_confirmation">Konfirmasi Password:</label>
        <input type="password" name="password_confirmation" required>
        <br>

        <button type="submit">Daftar</button>
    </form>

    <p>Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a></p>
</body>
</html>
