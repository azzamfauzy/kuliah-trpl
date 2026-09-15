<?php
// Pengaturan kredensial database XAMPP bawaan
$host     = "localhost";
$username = "root";
$password = "";
$database = "db_study_tracker";

// Membuat koneksi ke MySQL
$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek apakah koneksi berhasil atau gagal
if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Jika berhasil, kita bisa beri tanda komentar (tidak akan muncul di browser)
// echo "Koneksi Berhasil!";
?>
