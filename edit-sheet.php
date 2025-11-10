<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int)$_POST['id_sheet'];
  $nama = mysqli_real_escape_string($conn, $_POST['nama']);
  $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
  $saldo = (int)$_POST['saldo_awal'];
  $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);

  $query = "UPDATE sheet_info 
            SET nama='$nama', kategori='$kategori', saldo_awal='$saldo', deskripsi='$deskripsi' 
            WHERE id_sheet=$id";

  if (mysqli_query($conn, $query)) {
    echo "<script>alert('Sheet berhasil diperbarui!'); window.location='dashboard.php';</script>";
  } else {
    echo "<script>alert('Gagal memperbarui: " . mysqli_error($conn) . "'); history.back();</script>";
  }
} else {
  echo "<script>alert('Akses tidak sah!'); window.location='dashboard.php';</script>";
}
?>
