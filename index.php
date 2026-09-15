<?php
include 'koneksi.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Study Tracker - Maba TRPL</title>
</head>
<body>
    <h1>Dashboard Kuliah TRPL PNM Madiun</h1>
    <p>Daftar Mata Kuliah Semester 1:</p>
    
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
