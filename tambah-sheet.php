<?php
include 'koneksi.php';

// Pastikan form dikirim dengan metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Ambil data dari form
  $nama       = mysqli_real_escape_string($conn, $_POST['nama']);
  $kategori   = mysqli_real_escape_string($conn, $_POST['kategori']);
  $saldo_awal = (int)$_POST['saldo_awal'];
  $deskripsi  = mysqli_real_escape_string($conn, $_POST['deskripsi']);

  // Validasi sederhana
  if (empty($nama) || empty($kategori)) {
    echo "<script>alert('Nama dan kategori harus diisi!'); history.back();</script>";
    exit;
  }

  // Query insert
  $sql = "INSERT INTO sheet_info (nama, kategori, saldo_awal, deskripsi) 
          VALUES ('$nama', '$kategori', '$saldo_awal', '$deskripsi')";

  if (mysqli_query($conn, $sql)) {
    echo "<script>alert('Sheet baru berhasil ditambahkan!'); window.location='dashboard.php';</script>";
  } else {
    echo "<script>alert('Gagal menambahkan sheet: " . mysqli_error($conn) . "'); history.back();</script>";
  }
} else {
  echo "<script>alert('Akses tidak sah!'); window.location='dashboard.php';</script>";
}
?>
