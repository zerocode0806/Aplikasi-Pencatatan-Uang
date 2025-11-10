<?php
require 'vendor/autoload.php';
include 'koneksi.php';

use PhpOffice\PhpWord\TemplateProcessor;

// Pastikan id_sheet dikirim
if (!isset($_GET['id_sheet'])) {
    die("ID Sheet tidak ditemukan.");
}
$id_sheet = (int)$_GET['id_sheet'];

// Ambil info sheet
$q_sheet = mysqli_query($conn, "SELECT * FROM sheet_info WHERE id_sheet = $id_sheet");
$sheet = mysqli_fetch_assoc($q_sheet);
if (!$sheet) {
    die("Sheet tidak ditemukan di database.");
}

// Ambil data transaksi — urutkan tanggal ASC agar saldo terbaru di bawah
$q_data = mysqli_query($conn, "SELECT * FROM sheet_data WHERE id_sheet = $id_sheet ORDER BY tanggal ASC");
$data = [];
while ($row = mysqli_fetch_assoc($q_data)) {
    $data[] = [
        'no' => count($data) + 1,
        'tanggal' => date('d/m/Y', strtotime($row['tanggal'])),
        'keterangan' => $row['keterangan'],
        'pemasukan' => $row['pemasukan'] > 0 ? number_format($row['pemasukan'], 0, ',', '.') : '-',
        'pengeluaran' => $row['pengeluaran'] > 0 ? number_format($row['pengeluaran'], 0, ',', '.') : '-',
        'saldo' => number_format($row['saldo'], 0, ',', '.')
    ];
}

// Muat template Word
$templatePath = __DIR__ . '/kas-ipnu-ippnu.docx';
$template = new TemplateProcessor($templatePath);

// Isi data sheet ke header dokumen
$template->setValue('nama_sheet', htmlspecialchars($sheet['nama']));
$template->setValue('kategori', htmlspecialchars($sheet['kategori']));
$template->setValue('saldo_awal', number_format($sheet['saldo_awal'], 0, ',', '.'));

// Isi tabel transaksi (menggandakan baris)
if (!empty($data)) {
    $template->cloneRowAndSetValues('no', $data);
} else {
    // Jika tidak ada transaksi
    $template->setValue('no#1', '-');
    $template->setValue('tanggal#1', '-');
    $template->setValue('keterangan#1', 'Tidak ada data transaksi');
    $template->setValue('pemasukan#1', '-');
    $template->setValue('pengeluaran#1', '-');
    $template->setValue('saldo#1', '-');
}

// Simpan file hasil export
$outputFile = 'Laporan_Kas_' . preg_replace('/\s+/', '_', $sheet['nama']) . '.docx';
$template->saveAs($outputFile);

// Download otomatis
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=$outputFile");
header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
readfile($outputFile);

// Hapus file setelah diunduh (opsional)
unlink($outputFile);
exit;
?>
