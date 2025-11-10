<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int)$_POST['id_sheet'];

  // Jalankan dalam transaksi agar aman
  mysqli_begin_transaction($conn);

  try {
    // 1. Hapus semua data transaksi terkait sheet ini
    $delete_data = mysqli_query($conn, "DELETE FROM sheet_data WHERE id_sheet = $id");

    // 2. Hapus data dari tabel sheet_info
    $delete_info = mysqli_query($conn, "DELETE FROM sheet_info WHERE id_sheet = $id");

    // Jika keduanya berhasil, commit
    if ($delete_data && $delete_info) {
      mysqli_commit($conn);
      echo "<script>
        alert('Sheet dan semua data transaksi berhasil dihapus!');
        window.location='dashboard.php';
      </script>";
    } else {
      throw new Exception("Gagal menghapus data dari salah satu tabel.");
    }

  } catch (Exception $e) {
    // Jika gagal, rollback perubahan
    mysqli_rollback($conn);
    echo "<script>
      alert('Terjadi kesalahan: " . addslashes($e->getMessage()) . "');
      history.back();
    </script>";
  }
} else {
  echo "<script>
    alert('Akses tidak sah!');
    window.location='dashboard.php';
  </script>";
}
?>
