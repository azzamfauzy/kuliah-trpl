<?php
// 1. Panggil sistem otomatis dari Composer & koneksi database
require 'vendor/autoload.php';
include 'koneksi.php';

// 2. Aktifkan DOMPDF Namespace
use Dompdf\Dompdf;
use Dompdf\Options;

// 3. Setel pengaturan DOMPDF agar bisa membaca CSS dengan baik
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// 4. Ambil semua data catatan kuliah dari database
$ambil_materi = mysqli_query($koneksi, "SELECT tabel_materi.*, table_matkul.nama_matkul FROM tabel_materi JOIN table_matkul ON tabel_materi.id_matkul = table_matkul.id_matkul ORDER BY tabel_materi.tanggal_kuliah ASC");

// 5. Susun struktur tampilan kertas dokumen PDF menggunakan HTML & CSS internal
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.5; }
        h1 { text-align: center; color: #2c3e50; margin-bottom: 5px; }
        .sub-title { text-align: center; font-size: 14px; color: #7f8c8d; margin-bottom: 30px; }
        .card { background-color: #fff; border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px; }
        .meta-data { font-size: 12px; color: #2980b9; font-weight: bold; margin-bottom: 5px; }
        .judul-materi { font-size: 16px; color: #2c3e50; font-weight: bold; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .isi-catatan { font-size: 13px; color: #555; white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>RANGKUMAN MATERI KULIAH TRPL</h1>
    <div class="sub-title">Dicetak otomatis melalui Aplikasi Study Tracker - Azzam Fauzy</div>
';

$no = 1;
// Melakukan perulangan untuk memasukkan setiap baris catatan ke dalam dokumen PDF
while ($tampil = mysqli_fetch_assoc($ambil_materi)) {
    $tanggal = date('d-m-Y', strtotime($tampil['tanggal_kuliah']));
    $html .= '
    <div class="card">
        <div class="meta-data">[' . $no++ . '] ' . $tampil['nama_matkul'] . ' | Pertemuan ' . $tampil['pertemuan_ke'] . ' (' . $tanggal . ')</div>
        <div class="judul-materi">' . $tampil['judul_materi'] . '</div>
        <div class="isi-catatan">' . nl2br($tampil['catatan']) . '</div>
    </div>
    ';
}

$html .= '
</body>
</html>
';

// 6. Jalankan proses konversi HTML menjadi file PDF asli
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait'); // Setel ukuran kertas A4 posisi tegak
$dompdf->render();

// 7. Perintahkan browser untuk otomatis mengunduh filenya
$dompdf->stream("Rangkuman_Materi_Kuliah_Azzam.pdf", array("Attachment" => 1));
?>
