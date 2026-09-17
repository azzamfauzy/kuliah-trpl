<?php
include 'koneksi.php';

// 1. PROSES SIMPAN AGENDA TUGAS/UJIAN
if (isset($_POST['submit'])) {
    $id_matkul  = $_POST['id_matkul'];
    $jenis      = $_POST['jenis'];
    $deskripsi  = $_POST['deskripsi'];
    $deadline   = $_POST['deadline'];
    $status     = "Belum Selesai"; // Setelan awal saat tugas baru dibuat

    $simpan = mysqli_query($koneksi, "INSERT INTO tabel_tugas_ujian (id_matkul, jenis, deskripsi, deadline, status) VALUES ('$id_matkul', '$jenis', '$deskripsi', '$deadline', '$status')");

    if ($simpan) {
        header("Location: tugas.php");
    } else {
        echo "<script>alert('Gagal menambah agenda!');</script>";
    }
}

// 2. PROSES UPDATE STATUS TUGAS (SELESAI / BELUM)
if (isset($_GET['action']) && $_GET['action'] == 'update_status') {
    $id_agenda  = $_GET['id'];
    $status_sekarang = $_GET['status'];
    
    // Tukar status
    $status_baru = ($status_sekarang == 'Belum Selesai') ? 'Sudah Selesai' : 'Belum Selesai';
    
    $update = mysqli_query($koneksi, "UPDATE tabel_tugas_ujian SET status='$status_baru' WHERE id_agenda='$id_agenda'");
    
    if ($update) {
        header("Location: tugas.php");
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Tugas - Study Tracker</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>Manajemen Tugas & Ujian TRPL</h1>
    
    <!-- MENU NAVIGASI UTAMA -->
    <p style="text-align: center;">
        <a href="index.php" style="margin-right: 15px; font-weight: bold; color: #2c3e50;">📚 Data Matkul</a> | 
        <a href="materi.php" style="margin-right: 15px; margin-left: 15px; font-weight: bold; color: #2c3e50;">📝 Catatan Materi</a> |
        <a href="tugas.php" style="margin-left: 15px; font-weight: bold; color: #3498db;">📅 Agenda Tugas & Ujian</a>
    </p>
    
    <!-- FORM INPUT TUGAS / UJIAN -->
    <form action="" method="POST">
        <h3>Tambah Agenda Baru:</h3>
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
                <td>Jenis Agenda</td>
                <td>: 
                    <select name="jenis" style="width: 100%; padding: 8px; border-radius: 4px;" required>
                        <option value="Tugas">Tugas Harian</option>
                        <option value="Kuis">Kuis / Pop Quiz</option>
                        <option value="UTS">Ujian Tengah Semester (UTS)</option>
                        <option value="UAS">Ujian Akhir Semester (UAS)</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Deskripsi / Soal</td>
                <td>: <textarea name="deskripsi" rows="4" style="width: 100%; border-radius: 4px; padding: 8px;" placeholder="Contoh: Kerjakan modul 3 halaman 45 angka genap." required></textarea></td>
            </tr>
            <tr>
                <td>Batasan Waktu (Deadline)</td>
                <td>: <input type="datetime-local" name="deadline" required></td>
            </tr>
            <tr>
                <td></td>
                <td><button type="submit" name="submit">Simpan Agenda</button></td>
            </tr>
        </table>
    </form>

    <hr>

    <!-- TABEL MONITORING TUGAS & UJIAN -->
    <h3 style="text-align: center;">Daftar Deadline Kuliah Terdekat:</h3>
    <table>
        <tr>
            <th>No</th>
            <th>Mata Kuliah</th>
            <th>Jenis</th>
            <th>Deskripsi Tugas / Ujian</th>
            <th>Deadline</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php
        $no = 1;
        // Ambil data gabungan tabel tugas dan matkul, diurutkan dari deadline paling dekat
        $ambil_tugas = mysqli_query($koneksi, "SELECT tabel_tugas_ujian.*, table_matkul.nama_matkul FROM tabel_tugas_ujian JOIN table_matkul ON tabel_tugas_ujian.id_matkul = table_matkul.id_matkul ORDER BY tabel_tugas_ujian.deadline ASC");
        
        while ($tampil = mysqli_fetch_assoc($ambil_tugas)) {
            // Logika pewarnaan status biar menarik
            $warna_status = ($tampil['status'] == 'Sudah Selesai') ? 'green' : 'red';
            $teks_tombol  = ($tampil['status'] == 'Sudah Selesai') ? 'Mark as Undone 🔴' : 'Mark as Done 🟢';
        ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $tampil['nama_matkul']; ?></td>
            <td><strong><?php echo $tampil['jenis']; ?></strong></td>
            <td><?php echo nl2br($tampil['deskripsi']); ?></td>
            <td><span style="color: #c0392b; font-weight: bold;"><?php echo date('d-m-Y H:i', strtotime($tampil['deadline'])); ?> WIB</span></td>
            <td><strong style="color: <?php echo $warna_status; ?>;"><?php echo $tampil['status']; ?></strong></td>
            <td>
                <!-- Link tombol aksi yang memicu fungsi Update Status di bagian PHP atas -->
                <a href="tugas.php?action=update_status&id=<?php echo $tampil['id_agenda']; ?>&status=<?php echo $tampil['status']; ?>" style="text-decoration: none; padding: 5px 10px; background-color: #ecf0f1; border-radius: 4px; font-size: 12px; color: #333; font-weight: bold; border: 1px solid #bdc3c7;">
                    <?php echo $teks_tombol; ?>
                </a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
