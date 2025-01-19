<?php
$host = 'localhost';
$db = 'silarusa_db';
$user = 'root';
$pass = '';

// Koneksi ke database
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_book'])) {
        // Tambah buku
        $isbn = $_POST['isbn'];
        $title = $_POST['title'];
        $publisher = $_POST['publisher'];
        $author = $_POST['author'];

        // Cek apakah kombinasi title-author sudah ada
        $check_stmt = $conn->prepare("SELECT * FROM books WHERE title = ? AND author = ?");
        $check_stmt->bind_param("ss", $title, $author);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            echo "Buku dengan judul dan penulis tersebut sudah ada!";
        } else {
            $stmt = $conn->prepare("INSERT INTO books (isbn, title, publisher, author) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $isbn, $title, $publisher, $author);

            if ($stmt->execute()) {
                echo "Buku berhasil ditambahkan!";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        $check_stmt->close();

    } elseif (isset($_POST['delete_book'])) {
        // Hapus buku
        $title = $_POST['title'];
        $author = $_POST['author'];

        $stmt = $conn->prepare("DELETE FROM books WHERE title = ? AND author = ?");
        $stmt->bind_param("ss", $title, $author);

        if ($stmt->execute()) {
            echo "Buku berhasil dihapus!";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();

    } elseif (isset($_POST['edit_book'])) {
        // Edit buku
        $title = $_POST['title'];
        $author = $_POST['author'];
        $new_title = $_POST['new_title'] ?: $title;
        $new_publisher = $_POST['new_publisher'];
        $new_author = $_POST['new_author'] ?: $author;

        $stmt = $conn->prepare("UPDATE books SET title = ?, publisher = ?, author = ? WHERE title = ? AND author = ?");
        $stmt->bind_param("sssss", $new_title, $new_publisher, $new_author, $title, $author);

        if ($stmt->execute()) {
            echo "Buku berhasil diperbarui!";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();

    } elseif (isset($_POST['show_books'])) {
        // Tampilkan semua buku
        $result = $conn->query("SELECT * FROM books");
        echo "<h3>Data Buku:</h3>";
        echo "<table border='1'>
                <tr>
                    <th>ID</th>
                    <th>ISBN</th>
                    <th>Title</th>
                    <th>Publisher</th>
                    <th>Author</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['isbn']}</td>
                    <td>{$row['title']}</td>
                    <td>{$row['publisher']}</td>
                    <td>{$row['author']}</td>
                  </tr>";
        }
        echo "</table>";

    } elseif (isset($_POST['search_books'])) {
        // Pencarian buku
        $search_field = $_POST['search_field'];
        $search_value = $_POST['search_value'];

        $stmt = $conn->prepare("SELECT * FROM books WHERE $search_field LIKE ?");
        $search_value = "%" . $search_value . "%";
        $stmt->bind_param("s", $search_value);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<h3>Hasil Pencarian:</h3>";
            echo "<table border='1'>
                    <tr>
                        <th>ID</th>
                        <th>ISBN</th>
                        <th>Title</th>
                        <th>Publisher</th>
                        <th>Author</th>
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['isbn']}</td>
                        <td>{$row['title']}</td>
                        <td>{$row['publisher']}</td>
                        <td>{$row['author']}</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "Tidak ada hasil yang ditemukan.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>CRUD Buku</title>
</head>
<body>
    <h1>CRUD Buku</h1>

    <!-- Form tambah buku -->
    <h2>Tambah Buku</h2>
    <form method="post">
        <label>ISBN:</label>
        <input type="number" name="isbn" required><br>
        <label>Title:</label>
        <input type="text" name="title" required><br>
        <label>Publisher:</label>
        <input type="text" name="publisher"><br>
        <label>Author:</label>
        <input type="text" name="author" required><br>
        <button type="submit" name="add_book">Tambah</button>
    </form>

    <!-- Form hapus buku -->
    <h2>Hapus Buku</h2>
    <form method="post">
        <label>Title:</label>
        <input type="text" name="title" required><br>
        <label>Author:</label>
        <input type="text" name="author" required><br>
        <button type="submit" name="delete_book">Hapus</button>
    </form>

    <!-- Form edit buku -->
    <h2>Edit Buku</h2>
    <form method="post">
        <label>Cari Title:</label>
        <input type="text" name="title" required><br>
        <label>Cari Author:</label>
        <input type="text" name="author" required><br>
        <label>Title Baru:</label>
        <input type="text" name="new_title"><br>
        <label>Publisher Baru:</label>
        <input type="text" name="new_publisher"><br>
        <label>Author Baru:</label>
        <input type="text" name="new_author"><br>
        <button type="submit" name="edit_book">Edit</button>
    </form>

    <!-- Form tampilkan semua buku -->
    <h2>Tampilkan Semua Buku</h2>
    <form method="post">
        <button type="submit" name="show_books">Tampilkan</button>
    </form>

    <!-- Form pencarian buku -->
    <h2>Cari Buku</h2>
    <form method="post">
        <label>Cari Berdasarkan:</label>
        <select name="search_field">
            <option value="title">Title</option>
            <option value="publisher">Publisher</option>
            <option value="author">Author</option>
        </select><br>
        <label>Masukkan Kata Kunci:</label>
        <input type="text" name="search_value" required><br>
        <button type="submit" name="search_books">Cari</button>
    </form>
</body>
</html>
