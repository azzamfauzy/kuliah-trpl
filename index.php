<?php
include 'koneksi.php';

// Variabel penampung untuk mode Edit
$id_edit = ""; $nama_edit = ""; $semester_edit = ""; $dosen_edit = "";
$mode_edit = false;

// --- KODE BARU: HITUNG STATISTIK OTOMATIS ---
// 1. Hitung Total Mata Kuliah
$query_matkul = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM table_matkul");
$data_matkul  = mysqli_fetch_assoc($query_matkul);
$total_matkul = $data_matkul['total'];

// 2. Hitung Total Catatan Materi
$query_materi = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tabel_materi");
$data_materi  = mysqli_fetch_assoc($query_materi);
$total_materi = $data_materi['total'];

// 3. Hitung Utang Tugas (Belum Selesai)
$query_belum  = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tabel_tugas_ujian WHERE status='Belum Selesai'");
$data_belum   = mysqli_fetch_assoc($query_belum);
$utang_tugas  = $data_belum['total'];

// 4. Hitung Tugas Beres (Sudah Selesai)
$query_beres  = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tabel_tugas_ujian WHERE status='Sudah Selesai'");
$data_beres   = mysqli_fetch_assoc($query_beres);
$tugas_beres  = $data_beres['total'];


// 1. PROSES SAAT TOMBOL "SIMPAN" ATAU "PERBARUI" DI-KLIK
if (isset($_POST['submit'])) {
    $nama_matkul = $_POST['nama_matkul'];
    $semester    = $_POST['semester'];
    $dosen       = $_POST['dosen'];

    if (isset($_POST['mode_edit']) && $_POST['mode_edit'] == 'true') {
        $id_matkul = $_POST['id_matkul'];
        $update = mysqli_query($koneksi, "UPDATE table_matkul SET nama_matkul='$nama_matkul', semester='$semester', dosen='$dosen' WHERE id_matkul='$id_matkul'");
        if ($update) header("Location: index.php");
    } else {
        $simpan = mysqli_query($koneksi, "INSERT INTO table_matkul (nama_matkul, semester, dosen) VALUES ('$nama_matkul', '$semester', '$dosen')");
        if ($simpan) header("Location: index.php");
    }
}

// 2. PROSES SAAT TOMBOL "HAPUS" DI-KLIK
if (isset($_GET['action']) && $_GET['action'] == 'hapus') {
    $id_matkul = $_GET['id'];
    $hapus = mysqli_query($koneksi, "DELETE FROM table_matkul WHERE id_matkul = '$id_matkul'");
    if ($hapus) header("Location: index.php");
}

// 3. PROSES MODE EDIT
if (isset($_GET['action']) && $_GET['action'] == 'edit') {
    $id_matkul = $_GET['id'];
    $mode_edit = true;
    $ambil_edit = mysqli_query($koneksi, "SELECT * FROM table_matkul WHERE id_matkul='$id_matkul'");
    $data_edit = mysqli_fetch_assoc($ambil_edit);
    if ($data_edit) {
        $id_edit = $data_edit['id_matkul'];
        $nama_edit = $data_edit['nama_matkul'];
        $semester_edit = $data_edit['semester'];
        $dosen_edit = $data_edit['dosen'];
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

    <!-- KODE BARU: CONTAINER KOTAK STATISTIK -->
    <div class="stat-container">
        <div class="stat-card blue">
            <h3>📚 Total Matkul</h3>
            <p><?php echo $total_matkul; ?></p>
        </div>
        <div class="stat-card purple">
            <h3>📝 Catatan Materi</h3>
            <p><?php echo $total_materi; ?></p>
        </div>
        <div class="stat-card red">
            <h3>🔴 Utang Tugas</h3>
            <p><?php echo $utang_tugas; ?></p>
        </div>
        <div class="stat-card green">
            <h3>🟢 Tugas Beres</h3>
            <p><?php echo $tugas_beres; ?></p>
        </div>
    </div>
    
    <!-- FORM INPUT DATA -->
    <form action="" method="POST">
        <h3><?php echo $mode_edit ? "Edit Mata Kuliah:" : "Tambah Mata Kuliah Baru:"; ?></h3>
        <input type="hidden" name="id_matkul" value="<?php echo $id_edit; ?>">
        <input type="hidden" name="mode_edit" value="<?php echo $mode_edit ? 'true' : 'false'; ?>">
        
        <table border="0" cellpadding="5">
            <tr>
                <td>Nama Mata Kuliah</td>
                <td>: <input type="text" name="nama_matkul" value="<?php echo $nama_edit; ?>" required></td>
            </tr>
            <tr>
                <td>Semester</td>
                <td>: <input type="number" name="semester" min="1" max="8" value="<?php echo $semester_edit; ?>" required></td>
            </tr>
            <tr>
                <td>Dosen Pengampu</td>
                <td>: <input type="text" name="dosen" value="<?php echo $dosen_edit; ?>" required></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit" name="submit"><?php echo $mode_edit ? "Perbarui Matkul 💾" : "Simpan Matkul"; ?></button>
                    <?php if($mode_edit): ?>
                        <a href="index.php" style="display: block; text-align: center; margin-top: 10px; color: #7f8c8d; text-decoration: none; font-size: 14px;">❌ Batal Edit</a>
                    <?php endif; ?>
                </td>
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
                <a href="index.php?action=edit&id=<?php echo $tampil['id_matkul']; ?>" style="color: #d35400; font-weight: bold; text-decoration: none; margin-right: 10px;">Edit ✏️</a> | 
                <a href="index.php?action=hapus&id=<?php echo $tampil['id_matkul']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus mata kuliah ini?')" style="color: #c0392b; font-weight: bold; text-decoration: none;">Hapus 🗑️</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
