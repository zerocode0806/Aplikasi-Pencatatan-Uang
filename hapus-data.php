<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_data = (int)$_POST['id_data'];
  $id_sheet = (int)$_POST['id_sheet'];

  // Pastikan data ada
  $cek = mysqli_query($conn, "SELECT * FROM sheet_data WHERE id_data = $id_data");
  if (mysqli_num_rows($cek) === 0) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='detail-sheet.php?id_sheet=$id_sheet';</script>";
    exit;
  }

  // Hapus data
  $hapus = mysqli_query($conn, "DELETE FROM sheet_data WHERE id_data = $id_data");
  if ($hapus) {
    echo "<script>alert('Transaksi berhasil dihapus!'); window.location='detail-sheet.php?id_sheet=$id_sheet';</script>";
  } else {
    echo "<script>alert('Gagal menghapus data: " . mysqli_error($conn) . "'); history.back();</script>";
  }
} else {
  echo "<script>alert('Akses tidak sah!'); window.location='index.php';</script>";
}
?>
