<?php
include 'koneksi.php';

// Proses saat tombol "Simpan" di-klik
if (isset($_POST['submit'])) {
    $nama_matkul = $_POST['nama_matkul'];
    $semester    = $_POST['semester'];
    $dosen       = $_POST['dosen'];

    // Perintah SQL untuk memasukkan data ke tabel (pastikan memakai table_matkul dengan huruf 'e')
    $simpan = mysqli_query($koneksi, "INSERT INTO table_matkul (nama_matkul, semester, dosen) VALUES ('$nama_matkul', '$semester', '$dosen')");

    if ($simpan) {
        // Refresh halaman otomatis jika berhasil simpan
        header("Location: index.php");
    } else {
        echo "<script>alert('Gagal menambah data!');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Study Tracker - Maba TRPL</title>
</head>
<body>
    <h1>Dashboard Kuliah TRPL PNM Madiun</h1>
    
    <!-- FORM INPUT DATA -->
    <h3>Tambah Mata Kuliah Baru:</h3>
    <form action="" method="POST">
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
    <h3>Daftar Mata Kuliah:</h3>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>No</th>
            <th>Nama Mata Kuliah</th>
            <th>Semester</th>
            <th>Dosen Pengampu</th>
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
        </tr>
        <?php } ?>
    </table>
</body>
</html>
