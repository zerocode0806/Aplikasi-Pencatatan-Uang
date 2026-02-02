<?php
require 'vendor/autoload.php';
include 'koneksi.php';

use PhpOffice\PhpWord\TemplateProcessor;

if (!isset($_GET['id_sheet'])) {
    exit("ID Sheet tidak ditemukan.");
}

$id_sheet = (int)$_GET['id_sheet'];

$q_sheet = mysqli_query($conn, "SELECT * FROM sheet_info WHERE id_sheet = $id_sheet");
$sheet = mysqli_fetch_assoc($q_sheet);
if (!$sheet) {
    exit("Sheet tidak ditemukan.");
}

$q_data = mysqli_query($conn, "SELECT * FROM sheet_data WHERE id_sheet = $id_sheet ORDER BY tanggal ASC");

$data = [];
$no = 1;
while ($row = mysqli_fetch_assoc($q_data)) {
    $data[] = [
        'no' => $no++,
        'tanggal' => date('d/m/Y', strtotime($row['tanggal'])),
        'keterangan' => $row['keterangan'],
        'pemasukan' => $row['pemasukan'] > 0 ? number_format($row['pemasukan'], 0, ',', '.') : '-',
        'pengeluaran' => $row['pengeluaran'] > 0 ? number_format($row['pengeluaran'], 0, ',', '.') : '-',
        'saldo' => number_format($row['saldo'], 0, ',', '.')
    ];
}

$template = new TemplateProcessor(__DIR__ . '/kas-ipnu-ippnu.docx');

$template->setValue('nama_sheet', $sheet['nama']);
$template->setValue('kategori', $sheet['kategori']);
$template->setValue('saldo_awal', number_format($sheet['saldo_awal'], 0, ',', '.'));

if (!empty($data)) {
    $template->cloneRowAndSetValues('no', $data);
}

$outputFile = tempnam(sys_get_temp_dir(), 'kas_') . '.docx';
$template->saveAs($outputFile);

/* PENTING: bersihkan buffer sebelum header */
ob_end_clean(); // ganti ob_clean()

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="Laporan_Kas_'.$sheet['nama'].'.docx"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($outputFile));

readfile($outputFile);
unlink($outputFile);
exit;

