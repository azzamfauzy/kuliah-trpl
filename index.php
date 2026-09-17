<?php
include 'koneksi.php';

// 1. PROSES SAAT TOMBOL "SIMPAN" DI-KLIK
if (isset($_POST['submit'])) {
    $nama_matkul = $_POST['nama_matkul'];
    $semester    = $_POST['semester'];
    $dosen       = $_POST['dosen'];

    $simpan = mysqli_query($koneksi, "INSERT INTO table_matkul (nama_matkul, semester, dosen) VALUES ('$nama_matkul', '$semester', '$dosen')");

    if ($simpan) {
        header("Location: index.php");
    } else {
        echo "<script>alert('Gagal menambah data!');</script>";
    }
}

// 2. PROSES SAAT TOMBOL "HAPUS" DI-KLIK
if (isset($_GET['action']) && $_GET['action'] == 'hapus') {
    $id_matkul = $_GET['id'];

    // Perintah SQL untuk menghapus data berdasarkan ID
    $hapus = mysqli_query($koneksi, "DELETE FROM table_matkul WHERE id_matkul = '$id_matkul'");

    if ($hapus) {
        header("Location: index.php");
    } else {
        echo "<script>alert('Gagal menghapus data!');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Study Tracker - Maba TRPL</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>Dashboard Kuliah TRPL PNM Madiun</h1>
    
    <!-- MENU NAVIGASI UTAMA -->
    <p style="text-align: center;">
        <a href="index.php" style="margin-right: 15px; font-weight: bold; color: #3498db;">📚 Data Matkul</a> | 
        <a href="materi.php" style="margin-right: 15px; margin-left: 15px; font-weight: bold; color: #2c3e50;">📝 Catatan Materi</a> |
        <a href="tugas.php" style="margin-left: 15px; font-weight: bold; color: #2c3e50;">📅 Agenda Tugas & Ujian</a>
    </p>
    
    <!-- FORM INPUT DATA -->
    <form action="" method="POST">
        <h3>Tambah Mata Kuliah Baru:</h3>
        <table border="0" cellpadding="5">
            <tr>
                <td>Nama Mata Kuliah</td>
                <td>: <input type="text" name="nama_matkul" required></td>
            </tr>
            <tr>
                <td>Semester</td>
                <td>: <input type="number" name="semester" min="1" max="8" required></td>
            </tr>
            <tr>
                <td>Dosen Pengampu</td>
                <td>: <input type="text" name="dosen" required></td>
            </tr>
            <tr>
                <td></td>
                <td><button type="submit" name="submit">Simpan Matkul</button></td>
            </tr>
        </table>
    </form>

    <hr>

    <!-- TABEL TAMPIL DATA -->
    <h3 style="text-align: center;">Daftar Mata Kuliah:</h3>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>No</th>
            <th>Nama Mata Kuliah</th>
            <th>Semester</th>
            <th>Dosen Pengampu</th>
            <th>Aksi</th>
        </tr>
        <?php
        $no = 1;
        $ambil_data = mysqli_query($koneksi, "SELECT * FROM table_matkul");
        while ($tampil = mysqli_fetch_assoc($ambil_data)) {
        ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $tampil['nama_matkul']; ?></td>
            <td><?php echo $tampil['semester']; ?></td>
            <td><?php echo $tampil['dosen']; ?></td>
            <td>
                <!-- Tombol Hapus dengan konfirmasi Javascript agar tidak tidak sengaja tertekan -->
                <a href="index.php?action=hapus&id=<?php echo $tampil['id_matkul']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus mata kuliah ini?')" style="color: #c0392b; font-weight: bold; text-decoration: none;">Hapus 🗑️</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
