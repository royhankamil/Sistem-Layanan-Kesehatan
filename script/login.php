<?php
session_start();
$servername = "localhost";  // Ganti dengan server database Anda
$username = "root";         // Ganti dengan username database
$password = "";             // Ganti dengan password database
$dbname = "mydatabase";     // Ganti dengan nama database Anda

// Koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Koneksi database gagal: " . $conn->connect_error]));
}

// Ambil data dari form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST["email"]);
    $pass = trim($_POST["password"]);

    if (empty($user) || empty($pass)) {
        echo json_encode(["status" => "error", "message" => "Username dan password harus diisi."]);
        exit();
    }

    // Cek user di database
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        if (password_verify($pass, $row["password"])) {
            $_SESSION["user_id"] = $row["id"];
            echo json_encode(["status" => "success", "message" => "Login berhasil!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Password salah!"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Username tidak ditemukan!"]);
    }

    $stmt->close();
}

$conn->close();
?>
