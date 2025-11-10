<?php
include 'koneksi.php';

$id_sheet = $_POST['id_sheet'];
$tanggal = $_POST['tanggal'];
$tipe = $_POST['tipe'];
$keterangan = $_POST['keterangan'];
$nominal = (int)$_POST['nominal'];

// Ambil saldo terakhir
$q = mysqli_query($conn, "SELECT saldo FROM sheet_data WHERE id_sheet=$id_sheet ORDER BY id_data DESC LIMIT 1");
$last = mysqli_fetch_assoc($q);
$saldo_terakhir = $last ? $last['saldo'] : 0;

// Hitung saldo baru
if ($tipe == 'pemasukan') {
  $saldo_baru = $saldo_terakhir + $nominal;
  $pemasukan = $nominal;
  $pengeluaran = 0;
} else {
  $saldo_baru = $saldo_terakhir - $nominal;
  $pemasukan = 0;
  $pengeluaran = $nominal;
}

// Simpan data
mysqli_query($conn, "INSERT INTO sheet_data (id_sheet, tanggal, keterangan, pemasukan, pengeluaran, saldo)
VALUES ('$id_sheet', '$tanggal', '$keterangan', '$pemasukan', '$pengeluaran', '$saldo_baru')");

header("Location: detail-sheet.php?id_sheet=$id_sheet");
exit;
?>
