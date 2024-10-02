<?php
$koneksi = new mysqli("localhost", "root", "", "kasir");

if (!isset($_SESSION['id_user'])) {
    die("Anda harus login untuk mengubah password.");
}


// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$old_password = isset($_POST['old_password']) ? $_POST['old_password'] : '';
$new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

// Misalnya user ID disimpan di session setelah login
$id_user = $_SESSION['id_user'];

// Validasi jika password baru sama dengan konfirmasi password
if ($new_password !== $confirm_password) {
    die("Password baru dan konfirmasi password tidak cocok.");
}

// Ambil password lama dari database
$sql = "SELECT password FROM user WHERE id_user = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$stmt->bind_result($hashed_password);
$stmt->fetch();
$stmt->close();

// Verifikasi password lama
if (!password_verify($old_password, $hashed_password)) {
    die("Password lama salah.");
}

// Hash password baru
$new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

// Update password di database
$sql = "UPDATE user SET password = ? WHERE id_user = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("si", $new_hashed_password, $id_user);

if ($stmt->execute()) {
    echo "Password berhasil diubah.";
} else {
    echo "Terjadi kesalahan saat mengganti password.";
}

$stmt->close();
$koneksi->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password</title>
</head>
<body>
    <h2>Form Ganti Password</h2>
    <form action="ganti_password.php" method="POST">
    <label for="old_password">Password Lama:</label>
    <input type="password" name="old_password" required><br><br>

    <label for="new_password">Password Baru:</label>
    <input type="password" name="new_password" required><br><br>

    <label for="confirm_password">Konfirmasi Password Baru:</label>
    <input type="password" name="confirm_password" required><br><br>

    <input type="submit" value="Ganti Password">
</form>
</body>
</html>
