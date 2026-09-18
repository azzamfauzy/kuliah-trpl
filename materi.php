<?php
include 'koneksi.php';

// Variabel penampung untuk mode Edit
$id_edit = ""; $id_matkul_edit = ""; $pertemuan_edit = ""; $judul_edit = ""; $catatan_edit = ""; $tanggal_edit = "";
$mode_edit = false;

// 1. PROSES SIMPAN / UPDATE CATATAN MATERI
if (isset($_POST['submit'])) {
    $id_matkul       = $_POST['id_matkul'];
    $pertemuan_ke    = $_POST['pertemuan_ke'];
    $judul_materi    = $_POST['judul_materi'];
    $catatan         = $_POST['catatan'];
    $tanggal_kuliah  = $_POST['tanggal_kuliah'];

    if (isset($_POST['mode_edit']) && $_POST['mode_edit'] == 'true') {
        $id_materi = $_POST['id_materi'];
        $update = mysqli_query($koneksi, "UPDATE tabel_materi SET id_matkul='$id_matkul', pertemuan_ke='$pertemuan_ke', judul_materi='$judul_materi', catatan='$catatan', tanggal_kuliah='$tanggal_kuliah' WHERE id_materi='$id_materi'");
        if ($update) header("Location: materi.php");
    } else {
        $simpan = mysqli_query($koneksi, "INSERT INTO tabel_materi (id_matkul, pertemuan_ke, judul_materi, catatan, tanggal_kuliah) VALUES ('$id_matkul', '$pertemuan_ke', '$judul_materi', '$catatan', '$tanggal_kuliah')");
        if ($simpan) header("Location: materi.php");
    }
}

// 2. PROSES HAPUS CATATAN MATERI
if (isset($_GET['action']) && $_GET['action'] == 'hapus') {
    $id_materi = $_GET['id'];
    $hapus = mysqli_query($koneksi, "DELETE FROM tabel_materi WHERE id_materi = '$id_materi'");
    if ($hapus) header("Location: materi.php");
}

// 3. PROSES AMBIL DATA UNTUK MODE EDIT
if (isset($_GET['action']) && $_GET['action'] == 'edit') {
    $id_materi = $_GET['id'];
    $mode_edit = true;
    $ambil_edit = mysqli_query($koneksi, "SELECT * FROM tabel_materi WHERE id_materi='$id_materi'");
    $data_edit = mysqli_fetch_assoc($ambil_edit);
    if ($data_edit) {
        $id_edit          = $data_edit['id_materi'];
        $id_matkul_edit   = $data_edit['id_matkul'];
        $pertemuan_edit   = $data_edit['pertemuan_ke'];
        $judul_edit       = $data_edit['judul_materi'];
        $catatan_edit     = $data_edit['catatan'];
        $tanggal_edit     = $data_edit['tanggal_kuliah'];
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
    
    <p style="text-align: center;">
        <a href="index.php" style="margin-right: 15px; font-weight: bold; color: #2c3e50;">📚 Data Matkul</a> | 
        <a href="materi.php" style="margin-right: 15px; margin-left: 15px; font-weight: bold; color: #3498db;">📝 Catatan Materi</a> |
        <a href="tugas.php" style="margin-left: 15px; font-weight: bold; color: #2c3e50;">📅 Agenda Tugas & Ujian</a>
    </p>
    
    <form action="" method="POST">
        <h3><?php echo $mode_edit ? "Edit Catatan Kuliah:" : "Tambah Catatan Baru:"; ?></h3>
        <input type="hidden" name="id_materi" value="<?php echo $id_edit; ?>">
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
                <td>Pertemuan Ke-</td>
                <td>: <input type="number" name="pertemuan_ke" min="1" max="16" value="<?php echo $pertemuan_edit; ?>" required></td>
            </tr>
            <tr>
                <td>Judul Materi</td>
                <td>: <input type="text" name="judul_materi" value="<?php echo $judul_edit; ?>" required></td>
            </tr>
            <tr>
                <td>Isi Catatan / Notes</td>
                <td>: <textarea name="catatan" rows="5" style="width: 100%; border-radius: 4px; padding: 8px;" required><?php echo $catatan_edit; ?></textarea></td>
            </tr>
            <tr>
                <td>Tanggal Kuliah</td>
                <td>: <input type="date" name="tanggal_kuliah" value="<?php echo $tanggal_edit; ?>" required></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit" name="submit"><?php echo $mode_edit ? "Perbarui Catatan 💾" : "Simpan Catatan"; ?></button>
                    <?php if($mode_edit): ?>
                        <a href="materi.php" style="display: block; text-align: center; margin-top: 10px; color: #7f8c8d; text-decoration: none; font-size: 14px;">❌ Batal Edit</a>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </form>

    <hr>

    <h3 style="text-align: center;">Riwayat Materi Kuliah runtut:</h3>
    <table>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Mata Kuliah</th>
            <th>Pertemuan</th>
            <th>Judul Materi</th>
            <th>Catatan</th>
            <th>Aksi</th>
        </tr>
        <?php
        $no = 1;
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
            <td>
                <a href="materi.php?action=edit&id=<?php echo $tampil['id_materi']; ?>" style="color: #d35400; font-weight: bold; text-decoration: none; margin-right: 10px;">Edit ✏️</a> | 
                <a href="materi.php?action=hapus&id=<?php echo $tampil['id_materi']; ?>" onclick="return confirm('Hapus catatan ini?')" style="color: #c0392b; font-weight: bold; text-decoration: none;">Hapus 🗑️</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
