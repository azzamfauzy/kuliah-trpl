<?php
include 'koneksi.php';

// Proses menyimpan catatan materi baru
if (isset($_POST['simpan_materi'])) {
    $id_matkul       = $_POST['id_matkul'];
    $pertemuan_ke    = $_POST['pertemuan_ke'];
    $judul_materi    = $_POST['judul_materi'];
    $catatan         = $_POST['catatan'];
    $tanggal_kuliah  = $_POST['tanggal_kuliah'];

    // Memasukkan data ke tabel_materi
    $query = "INSERT INTO tabel_materi (id_matkul, pertemuan_ke, judul_materi, catatan, tanggal_kuliah) 
              VALUES ('$id_matkul', '$pertemuan_ke', '$judul_materi', '$catatan', '$tanggal_kuliah')";
    
    $simpan = mysqli_query($koneksi, $query);

    if ($simpan) {
        header("Location: materi.php");
    } else {
        echo "<script>alert('Gagal menyimpan materi!');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Catatan Materi Kuliah - TRPL</title>
</head>
<body>
    <h1>Catatan Materi Kuliah Harian</h1>
    <a href="index.php">⬅️ Kembali ke Daftar Matkul</a>
    <hr>

    <!-- FORM INPUT CATATAN MATERI -->
    <h3>Tambah Catatan Kuliah Baru:</h3>
    <form action="" method="POST">
        <table border="0" cellpadding="5">
            <tr>
                <td>Mata Kuliah</td>
                <td>: 
                    <select name="id_matkul" required>
                        <option value="">-- Pilih Mata Kuliah --</option>
                        <?php
                        // Mengambil daftar matkul dari tabel induk agar sinkron
                        $matkul = mysqli_query($koneksi, "SELECT * FROM table_matkul");
                        while($m = mysqli_fetch_assoc($matkul)) {
                            echo "<option value='".$m['id_matkul']."'>".$m['nama_matkul']." (Sem ".$m['semester'].")</option>";
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
                <td>: <input type="text" name="judul_materi" required style="width: 300px;"></td>
            </tr>
            <tr>
                <td>Catatan / Notes</td>
                <td>: <textarea name="catatan" rows="5" cols="40" placeholder="Ketik materi penting di sini..." required></textarea></td>
            </tr>
            <tr>
                <td>Tanggal Kuliah</td>
                <td>: <input type="date" name="tanggal_kuliah" required></td>
            </tr>
            <tr>
                <td></td>
                <td><button type="submit" name="simpan_materi">Simpan Catatan</button></td>
            </tr>
        </table>
    </form>

    <hr>

    <!-- TABEL UTAMA TAMPIL DATA (RUNTUT BERDASARKAN TANGGAL TERBARU) -->
    <h3>Riwayat Materi & Catatan Kuliah:</h3>
    <table border="1" cellpadding="10" cellspacing="0">
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
        // Menggunakan teknik JOIN untuk menggabungkan tabel_materi dan table_matkul 
        // ORDER BY tanggal_kuliah DESC artinya data yang tanggalnya paling baru akan muncul di atas
        $query_tampil = "SELECT tabel_materi.*, table_matkul.nama_matkul 
                         FROM tabel_materi 
                         JOIN table_matkul ON tabel_materi.id_matkul = table_matkul.id_matkul
                         ORDER BY tabel_materi.tanggal_kuliah DESC";
                         
        $ambil_materi = mysqli_query($koneksi, $query_tampil);
        
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
