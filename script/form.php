<?php
session_start();
$host = 'localhost';
$db = 'silarusa_db';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register'])) {
        $email = $_POST['email'];
        $name = $_POST['name'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $address = $_POST['address'];

        // Periksa apakah email sudah ada
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Email sudah terdaftar, gunakan email lain.";
        } else {
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO users (email, name, password, address) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $email, $name, $password, $address);
            if ($stmt->execute()) {
                $message = "Registrasi Berhasil!";
            } else {
                $message = "Terjadi kesalahan saat registrasi.";
            }
        }
        $stmt->close();

    } elseif (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Periksa apakah email ada
        $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            $message = "Email tidak ditemukan.";
        } else {
            $stmt->bind_result($hashed_password);
            $stmt->fetch();

            // Periksa password
            if (password_verify($password, $hashed_password)) {
                $_SESSION['email'] = $email;
                $message = "Login Berhasil!";
            } else {
                $message = "Password salah.";
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
    <?php if (isset($message)) echo "<p>$message</p>"; ?>

    <h2>Register</h2>
    <form method="post" action="">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="address">Address:</label>
        <input type="text" id="address" name="address" required>
        <br>
        <button type="submit" name="register">Register</button>
    </form>

    <h2>Login</h2>
    <form method="post" action="">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit" name="login">Login</button>
    </form>
</body>
</html>
