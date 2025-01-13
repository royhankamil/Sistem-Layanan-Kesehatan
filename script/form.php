<?php
session_start();
$host = 'localhost';
$db = 'libreadify_db';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register'])) {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        // Periksa apakah username sudah ada
        $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // echo 
            $message = "Username telah terpakai, gunakan username yang lain.";
        } else {
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            // echo 
            "Registrasi Berhasil!";
        }
        $stmt->close();

    } elseif (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Periksa apakah username ada
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            echo "Maaf, user tidak ditemukan.";
        } else {
            $stmt->bind_result($hashed_password);
            $stmt->fetch();

            // Periksa password
            if (password_verify($password, $hashed_password)) {
                $_SESSION['username'] = $username;
                echo "Login Berhasil!";
            } else {
                echo "Maaf, password Anda salah.";
            }
        }
        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Login and Registration System</title>
</head>
<body>
    <h2>Register</h2>
    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit" name="register">Register</button>
    </form>

    <h2>Login</h2>
    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit" name="login">Login</button>
        <div class="error_message"></div>
    </form>
</body>
</html>