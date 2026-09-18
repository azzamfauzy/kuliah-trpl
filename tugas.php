<?php
include 'koneksi.php';

$id_edit = ""; $id_matkul_edit = ""; $jenis_edit = ""; $deskripsi_edit = ""; $deadline_edit = "";
$mode_edit = false;

// 1. PROSES SIMPAN / UPDATE AGENDA TUGAS
if (isset($_POST['submit'])) {
    $id_matkul  = $_POST['id_matkul'];
    $jenis      = $_POST['jenis'];
    $deskripsi  = $_POST['deskripsi'];
    $deadline   = $_POST['deadline'];

    if (isset($_POST['mode_edit']) && $_POST['mode_edit'] == 'true') {
        $id_agenda = $_POST['id_agenda'];
        $update = mysqli_query($koneksi, "UPDATE tabel_tugas_ujian SET id_matkul='$id_matkul', jenis='$jenis', deskripsi='$deskripsi', deadline='$deadline' WHERE id_agenda='$id_agenda'");
        if ($update) header("Location: tugas.php");
    } else {
        $status = "Belum Selesai";
        $simpan = mysqli_query($koneksi, "INSERT INTO tabel_tugas_ujian (id_matkul, jenis, deskripsi, deadline, status) VALUES ('$id_matkul', '$jenis', '$deskripsi', '$deadline', '$status')");
        if ($simpan) header("Location: tugas.php");
    }
}

// 2. PROSES UPDATE STATUS TUGAS
if (isset($_GET['action']) && $_GET['action'] == 'update_status') {
    $id_agenda  = $_GET['id'];
    $status_sekarang = $_GET['status'];
    $status_baru = ($status_sekarang == 'Belum Selesai') ? 'Sudah Selesai' : 'Belum Selesai';
    $update = mysqli_query($koneksi, "UPDATE tabel_tugas_ujian SET status='$status_baru' WHERE id_agenda='$id_agenda'");
    if ($update) header("Location: tugas.php");
}

// 3. PROSES HAPUS AGENDA TUGAS
if (isset($_GET['action']) && $_GET['action'] == 'hapus') {
    $id_agenda = $_GET['id'];
    $hapus = mysqli_query($koneksi, "DELETE FROM tabel_tugas_ujian WHERE id_agenda = '$id_agenda'");
    if ($hapus) header("Location: tugas.php");
}

// 4. PROSES AMBIL DATA UNTUK MODE EDIT
if (isset($_GET['action']) && $_GET['action'] == 'edit') {
    $id_agenda = $_GET['id'];
    $mode_edit = true;
    $ambil_edit = mysqli_query($koneksi, "SELECT * FROM tabel_tugas_ujian WHERE id_agenda='$id_agenda'");
    $data_edit = mysqli_fetch_assoc($ambil_edit);
    if ($data_edit) {
        $id_edit          = $data_edit['id_agenda'];
        $id_matkul_edit   = $data_edit['id_matkul'];
        $jenis_edit       = $data_edit['jenis'];
        $deskripsi_edit   = $data_edit['deskripsi'];
        // Mengubah format tanggal agar pas di input datetime-local
        $deadline_edit    = date('Y-m-d\TH:i', strtotime($data_edit['deadline']));
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
    
    <p style="text-align: center;">
        <a href="index.php" style="margin-right: 15px; font-weight: bold; color: #2c3e50;">📚 Data Matkul</a> | 
        <a href="materi.php" style="margin-right: 15px; margin-left: 15px; font-weight: bold; color: #2c3e50;">📝 Catatan Materi</a> |
        <a href="tugas.php" style="margin-left: 15px; font-weight: bold; color: #3498db;">📅 Agenda Tugas & Ujian</a>
    </p>
    
    <form action="" method="POST">
        <h3><?php echo $mode_edit ? "Edit Agenda Kuliah:" : "Tambah Agenda Baru:"; ?></h3>
        <input type="hidden" name="id_agenda" value="<?php echo $id_edit; ?>">
        <input type="hidden" name="mode_edit" value="<?php echo $mode_edit ? 'true' : 'false'; ?>">
        
        <table border="0" cellpadding="5">
            <tr>
                <td>Mata Kuliah</td>
                <td>: 
                    <select name="id_matkul" style="width: 100%; padding: 8px; border-radius: 4px;" required>
                        <option value="">-- Pilih Mata Kuliah --</option>
                        <?php
                        $matkul = mysqli_query($koneksi, "SELECT * FROM table_matkul");
                        while($m = mysqli_fetch_assoc($matkul)) {
                            $selected = ($m['id_matkul'] == $id_matkul_edit) ? "selected" : "";
                            echo "<option value='".$m['id_matkul']."' $selected>".$m['nama_matkul']."</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Jenis Agenda</td>
                <td>: 
                    <select name="jenis" style="width: 100%; padding: 8px; border-radius: 4px;" required>
                        <option value="Tugas" <?php echo ($jenis_edit == 'Tugas') ? 'selected' : ''; ?>>Tugas Harian</option>
                        <option value="Kuis" <?php echo ($jenis_edit == 'Kuis') ? 'selected' : ''; ?>>Kuis / Pop Quiz</option>
                        <option value="UTS" <?php echo ($jenis_edit == 'UTS') ? 'selected' : ''; ?>>Ujian Tengah Semester (UTS)</option>
                        <option value="UAS" <?php echo ($jenis_edit == 'UAS') ? 'selected' : ''; ?>>Ujian Akhir Semester (UAS)</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Deskripsi / Soal</td>
                <td>: <textarea name="deskripsi" rows="4" style="width: 100%; border-radius: 4px; padding: 8px;" required><?php echo $deskripsi_edit; ?></textarea></td>
            </tr>
            <tr>
                <td>Batasan Waktu (Deadline)</td>
                <td>: <input type="datetime-local" name="deadline" value="<?php echo $deadline_edit; ?>" required></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit" name="submit"><?php echo $mode_edit ? "Perbarui Agenda 💾" : "Simpan Agenda"; ?></button>
                    <?php if($mode_edit): ?>
                        <a href="tugas.php" style="display: block; text-align: center; margin-top: 10px; color: #7f8c8d; text-decoration: none; font-size: 14px;">❌ Batal Edit</a>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </form>

    <hr>

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
        $ambil_tugas = mysqli_query($koneksi, "SELECT tabel_tugas_ujian.*, table_matkul.nama_matkul FROM tabel_tugas_ujian JOIN table_matkul ON tabel_tugas_ujian.id_matkul = table_matkul.id_matkul ORDER BY tabel_tugas_ujian.deadline ASC");
        while ($tampil = mysqli_fetch_assoc($ambil_tugas)) {
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
                <a href="tugas.php?action=update_status&id=<?php echo $tampil['id_agenda']; ?>&status=<?php echo $tampil['status']; ?>" style="text-decoration: none; padding: 5px 10px; background-color: #ecf0f1; border-radius: 4px; font-size: 12px; color: #333; font-weight: bold; border: 1px solid #bdc3c7;">
                    <?php echo $teks_tombol; ?>
                </a> | 
                <a href="tugas.php?action=edit&id=<?php echo $tampil['id_agenda']; ?>" style="color: #d35400; font-weight: bold; text-decoration: none; font-size: 14px; margin-right: 5px;">Edit ✏️</a> |
                <a href="tugas.php?action=hapus&id=<?php echo $tampil['id_agenda']; ?>" onclick="return confirm('Hapus agenda ini?')" style="color: #c0392b; font-weight: bold; text-decoration: none; font-size: 14px;">Hapus 🗑️</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
