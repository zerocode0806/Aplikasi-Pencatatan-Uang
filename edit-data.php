<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_data  = (int)$_POST['id_data'];
    $id_sheet = (int)$_POST['id_sheet'];
    $tanggal  = $_POST['tanggal'];
    $tipe     = $_POST['tipe'];
    $ket      = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $nominal  = (int)$_POST['nominal'];

    $pemasukan   = ($tipe === 'pemasukan') ? $nominal : 0;
    $pengeluaran = ($tipe === 'pengeluaran') ? $nominal : 0;

    /* ============================
       1. Ambil saldo sebelumnya
       ============================ */
    $q_prev = mysqli_query($conn, "
        SELECT saldo FROM sheet_data
        WHERE id_sheet = $id_sheet
          AND id_data < $id_data
        ORDER BY id_data DESC
        LIMIT 1
    ");

    $saldo_sebelumnya = 0;
    if ($row = mysqli_fetch_assoc($q_prev)) {
        $saldo_sebelumnya = $row['saldo'];
    }

    /* ============================
       2. Hitung saldo baru
       ============================ */
    $saldo_baru = $saldo_sebelumnya + $pemasukan - $pengeluaran;

    /* ============================
       3. Update transaksi yang diedit
       ============================ */
    mysqli_query($conn, "
        UPDATE sheet_data SET
            tanggal      = '$tanggal',
            keterangan   = '$ket',
            pemasukan    = $pemasukan,
            pengeluaran  = $pengeluaran,
            saldo        = $saldo_baru
        WHERE id_data = $id_data
    ");

    /* ============================
       4. Update saldo transaksi setelahnya
       ============================ */
    $q_next = mysqli_query($conn, "
        SELECT * FROM sheet_data
        WHERE id_sheet = $id_sheet
          AND id_data > $id_data
        ORDER BY id_data ASC
    ");

    $saldo_berjalan = $saldo_baru;

    while ($row = mysqli_fetch_assoc($q_next)) {
        $saldo_berjalan =
            $saldo_berjalan + $row['pemasukan'] - $row['pengeluaran'];

        mysqli_query($conn, "
            UPDATE sheet_data
            SET saldo = $saldo_berjalan
            WHERE id_data = {$row['id_data']}
        ");
    }

    echo "<script>
        alert('Transaksi berhasil diperbarui!');
        window.location='detail-sheet.php?id_sheet=$id_sheet';
    </script>";

} else {
    echo "<script>alert('Akses tidak sah'); window.location='index.php';</script>";
}
