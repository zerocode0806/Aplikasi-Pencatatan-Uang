<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_data   = (int)$_POST['id_data'];
  $id_sheet  = (int)$_POST['id_sheet'];
  $tanggal   = mysqli_real_escape_string($conn, $_POST['tanggal']);
  $tipe      = $_POST['tipe'];
  $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
  $nominal   = (int)$_POST['nominal'];

  // Siapkan nilai pemasukan/pengeluaran
  $pemasukan   = ($tipe === 'pemasukan') ? $nominal : 0;
  $pengeluaran = ($tipe === 'pengeluaran') ? $nominal : 0;

  // Update data transaksi
  $query = "UPDATE sheet_data 
            SET tanggal='$tanggal', keterangan='$keterangan',
                pemasukan=$pemasukan, pengeluaran=$pengeluaran
            WHERE id_data=$id_data";

  if (mysqli_query($conn, $query)) {
    echo "<script>alert('Transaksi berhasil diperbarui!'); window.location='detail-sheet.php?id_sheet=$id_sheet';</script>";
  } else {
    echo "<script>alert('Gagal memperbarui data: " . mysqli_error($conn) . "'); history.back();</script>";
  }
} else {
  echo "<script>alert('Akses tidak sah!'); window.location='index.php';</script>";
}
?>
