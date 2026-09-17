<?php
include 'koneksi.php';

// Proses saat tombol "Simpan Catatan" di-klik
if (isset($_POST['submit'])) {
    $id_matkul       = $_POST['id_matkul'];
    $pertemuan_ke    = $_POST['pertemuan_ke'];
    $judul_materi    = $_POST['judul_materi'];
    $catatan         = $_POST['catatan'];
    $tanggal_kuliah  = $_POST['tanggal_kuliah'];

    // Memasukkan data ke tabel materi
    $simpan = mysqli_query($koneksi, "INSERT INTO tabel_materi (id_matkul, pertemuan_ke, judul_materi, catatan, tanggal_kuliah) VALUES ('$id_matkul', '$pertemuan_ke', '$judul_materi', '$catatan', '$tanggal_kuliah')");

    if ($simpan) {
        header("Location: materi.php");
    } else {
        echo "<script>alert('Gagal menambah catatan!');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Catatan Materi - Study Tracker</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>Catatan Materi Kuliah Harian</h1>
    <p style="text-align: center;"><a href="index.php">⬅️ Kembali ke Dashboard Matkul</a></p>
    
    <!-- FORM INPUT CATATAN MATERI -->
    <form action="" method="POST">
        <h3>Tambah Catatan Baru:</h3>
        <table border="0" cellpadding="5">
            <tr>
                <td>Mata Kuliah</td>
                <td>: 
                    <select name="id_matkul" style="width: 100%; padding: 8px; border-radius: 4px;" required>
                        <option value="">-- Pilih Mata Kuliah --</option>
                        <?php
                        $matkul = mysqli_query($koneksi, "SELECT * FROM table_matkul");
                        while($m = mysqli_fetch_assoc($matkul)) {
                            echo "<option value='".$m['id_matkul']."'>".$m['nama_matkul']."</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Pertemuan Ke-</td>
                <td>: <input type="number" name="pertemuan_ke" min="1" max="16" required></td>
            </tr>
            <tr>
                <td>Judul Materi</td>
                <td>: <input type="text" name="judul_materi" required></td>
            </tr>
            <tr>
                <td>Isi Catatan / Notes</td>
                <td>: <textarea name="catatan" rows="5" style="width: 100%; border-radius: 4px; padding: 8px;" required></textarea></td>
            </tr>
            <tr>
                <td>Tanggal Kuliah</td>
                <td>: <input type="date" name="tanggal_kuliah" required></td>
            </tr>
            <tr>
                <td></td>
                <td><button type="submit" name="submit">Simpan Catatan</button></td>
            </tr>
        </table>
    </form>

    <hr>

    <!-- TABEL TAMPILAN CATATAN MATERI -->
    <h3 style="text-align: center;">Riwayat Materi Kuliah runtut:</h3>
    <table>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Mata Kuliah</th>
            <th>Pertemuan</th>
            <th>Judul Materi</th>
            <th>Catatan</th>
        </tr>
        <?php
        $no = 1;
        // Menggunakan SQL JOIN agar kita bisa memunculkan nama matkul, bukan sekadar angka ID
        $ambil_materi = mysqli_query($koneksi, "SELECT tabel_materi.*, table_matkul.nama_matkul FROM tabel_materi JOIN table_matkul ON tabel_materi.id_matkul = table_matkul.id_matkul ORDER BY tabel_materi.tanggal_kuliah DESC");

        while ($tampil = mysqli_fetch_assoc($ambil_materi)) {
        ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo date('d-m-Y', strtotime($tampil['tanggal_kuliah'])); ?></td>
            <td><?php echo $tampil['nama_matkul']; ?></td>
            <td>Ke-<?php echo $tampil['pertemuan_ke']; ?></td>
            <td><strong><?php echo $tampil['judul_materi']; ?></strong></td>
            <td><?php echo nl2br($tampil['catatan']); ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
