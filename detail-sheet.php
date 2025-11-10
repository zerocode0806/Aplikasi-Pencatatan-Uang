<?php
include 'koneksi.php';

session_start();
if (!isset($_SESSION['user'])) {
  header('Location: index.php');
  exit;
}

// Pastikan id_sheet dikirim
if (!isset($_GET['id_sheet'])) {
  die("Sheet tidak ditemukan.");
}
$id_sheet = (int)$_GET['id_sheet'];

// Ambil info sheet
$q_info = mysqli_query($conn, "SELECT * FROM sheet_info WHERE id_sheet = $id_sheet");
$sheet = mysqli_fetch_assoc($q_info);
if (!$sheet) {
  die("Sheet tidak ditemukan di database.");
}

// Ambil data transaksi sheet
$q_data = mysqli_query($conn, "SELECT * FROM sheet_data WHERE id_sheet = $id_sheet ORDER BY id_data ASC");

// Hitung total
$total_masuk = 0;
$total_keluar = 0;
while ($d = mysqli_fetch_assoc($q_data)) {
  $total_masuk += $d['pemasukan'];
  $total_keluar += $d['pengeluaran'];
}
$saldo_akhir = $sheet['saldo_awal'] + $total_masuk - $total_keluar;

// Reset pointer hasil query untuk ditampilkan di tabel
mysqli_data_seek($q_data, 0);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($sheet['nama']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --primary: #4f46e5;
      --success: #22c55e;
      --warning: #f59e0b;
      --danger: #ef4444;
      --text: #1e293b;
      --muted: #64748b;
      --bg: #f8fafc;
      --card: #ffffff;
      --radius: 12px;
      --shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    * { 
      font-family: "Inter", sans-serif;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body { 
      background: var(--bg);
      color: var(--text);
    }

    /* Navbar Modern & Minimalis */
    .navbar-custom {
      background: var(--card);
      box-shadow: var(--shadow);
      padding: 1rem 0;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .navbar-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .navbar-brand {
      font-weight: 700;
      color: var(--primary);
      font-size: 1.25rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      text-decoration: none;
    }

    .navbar-brand i {
      font-size: 1.5rem;
    }

    .navbar-menu {
      display: flex;
      gap: 0.75rem;
      align-items: center;
    }

    .hamburger {
      display: none;
      flex-direction: column;
      gap: 4px;
      background: none;
      border: none;
      cursor: pointer;
      padding: 0.5rem;
    }

    .hamburger span {
      width: 24px;
      height: 3px;
      background: var(--text);
      border-radius: 2px;
      transition: all 0.3s ease;
    }

    .hamburger.active span:nth-child(1) {
      transform: rotate(45deg) translate(6px, 6px);
    }

    .hamburger.active span:nth-child(2) {
      opacity: 0;
    }

    .hamburger.active span:nth-child(3) {
      transform: rotate(-45deg) translate(6px, -6px);
    }

    .mobile-menu {
      display: none;
      position: fixed;
      top: 65px;
      left: 0;
      right: 0;
      background: var(--card);
      box-shadow: var(--shadow);
      padding: 1rem;
      animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .mobile-menu.active {
      display: block;
    }

    .mobile-menu .btn-modern {
      width: 100%;
      margin-bottom: 0.5rem;
      justify-content: center;
      display: flex;
    }

    .btn-modern {
      border-radius: var(--radius);
      border: none;
      font-weight: 600;
      padding: 0.6rem 1.1rem;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      cursor: pointer;
      transition: all 0.2s ease;
      text-decoration: none;
      color: white;
      font-size: 0.875rem;
    }

    .btn-modern:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      color: white;
    }

    .btn-modern i {
      font-size: 1rem;
    }

    .btn-primary { background: var(--primary); }
    .btn-success { background: var(--success); }
    .btn-danger { background: var(--danger); }
    .btn-warning { background: var(--warning); }
    .btn-secondary { background: var(--muted); }

    /* Summary Cards */
    .summary-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .summary-card {
      background: var(--card);
      padding: 1.25rem;
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      text-align: center;
      transition: 0.2s;
    }

    .summary-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .summary-value {
      font-size: 1.6rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }

    .summary-label {
      font-size: 0.9rem;
      color: var(--muted);
    }

    .text-success { color: var(--success) !important; }
    .text-danger { color: var(--danger) !important; }
    .text-primary { color: var(--primary) !important; }

    /* Table Section */
    .table-card {
      background: var(--card);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      padding: 1.5rem;
      margin-bottom: 2rem;
    }

    .table-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .table-title {
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--text);
      margin: 0;
    }

    .search-bar input {
      border-radius: 8px;
      border: 1px solid #d1d5db;
      padding: 0.5rem 1rem;
      width: 250px;
    }

    .search-bar input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .table {
      margin-bottom: 0;
    }

    .table thead {
      background: #eef2ff;
      color: #374151;
    }

    .table thead th {
      font-weight: 600;
      vertical-align: middle;
      padding: 1rem;
      border: none;
    }

    .table tbody td {
      padding: 1rem;
      vertical-align: middle;
    }

    .table tbody tr {
      border-bottom: 1px solid #e5e7eb;
      transition: background 0.2s;
    }

    .table tbody tr:hover {
      background: #f9fafb;
    }

    .total-row {
      background: #f1f5f9 !important;
      font-weight: 700;
    }

    .total-row td {
      padding: 1rem !important;
      font-size: 1.05rem;
    }

    .btn-sm {
      padding: 0.375rem 0.75rem;
      font-size: 0.875rem;
    }

    /* Modal */
    .modal-content {
      border-radius: var(--radius);
      border: none;
      overflow: hidden;
    }

    .modal-header {
      padding: 1rem 1.25rem;
      border-bottom: none;
    }

    .modal-header.bg-primary {
      background: var(--primary) !important;
      color: white;
    }

    .modal-header.bg-warning {
      background: var(--warning) !important;
      color: white;
    }

    .modal-header.bg-danger {
      background: var(--danger) !important;
      color: white;
    }

    .modal-title {
      font-weight: 600;
      font-size: 1.125rem;
    }

    .modal-body {
      padding: 1.5rem;
    }

    .modal-footer {
      border-top: 1px solid #e5e7eb;
      padding: 1rem 1.5rem;
    }

    .form-label {
      font-weight: 600;
      font-size: 0.875rem;
      margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
      border-radius: 8px;
      border: 1px solid #d1d5db;
      padding: 0.625rem 0.875rem;
    }

    .form-control:focus, .form-select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    /* Container */
    .container {
      max-width: 1200px;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .navbar-container {
        padding: 0 1rem;
      }

      .hamburger {
        display: flex;
      }

      .navbar-menu {
        display: none;
      }

      .navbar-brand {
        font-size: 1rem;
      }

      .navbar-brand i {
        font-size: 1.125rem;
      }

      .summary-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
      }

      .summary-card {
        padding: 1rem;
      }

      .summary-value {
        font-size: 1.25rem;
      }

      .summary-label {
        font-size: 0.8125rem;
      }

      .table-header {
        flex-direction: column;
        align-items: flex-start;
      }

      .search-bar input {
        width: 100%;
      }

      .table-card {
        padding: 1rem;
      }

      .table thead th,
      .table tbody td {
        padding: 0.75rem 0.5rem;
        font-size: 0.875rem;
      }

      .btn-modern {
        font-size: 0.8125rem;
        padding: 0.5rem 0.875rem;
      }

      .container {
        padding-left: 1rem;
        padding-right: 1rem;
      }
    }

    @media (max-width: 576px) {
      .navbar-container {
        padding: 0 0.875rem;
      }

      .navbar-brand {
        font-size: 0.9375rem;
      }

      .summary-container {
        grid-template-columns: 1fr;
        gap: 0.625rem;
      }

      .summary-card {
        padding: 0.875rem;
      }

      .summary-value {
        font-size: 1.125rem;
      }

      .table-title {
        font-size: 1.125rem;
      }

      /* Hide some table columns on mobile */
      .table thead th:nth-child(6),
      .table tbody td:nth-child(6) {
        display: none;
      }

      .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
      }
    }

    @media (min-width: 769px) and (max-width: 991px) {
      .navbar-container {
        padding: 0 1.5rem;
      }

      .summary-container {
        grid-template-columns: repeat(3, 1fr);
      }
    }
  </style>
</head>
<body>

<!-- Navbar dengan Hamburger Menu -->
<nav class="navbar-custom">
  <div class="navbar-container">
    <a href="dashboard.php" class="navbar-brand">
      <i class="fas fa-file-invoice"></i>
      <span><?= htmlspecialchars($sheet['nama']) ?></span>
    </a>

    <!-- Desktop Menu -->
    <div class="navbar-menu">
      <a href="dashboard.php" class="btn btn-secondary btn-modern">
        <i class="fas fa-arrow-left"></i> Kembali
      </a>
      <button class="btn btn-success btn-modern" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
        <i class="fas fa-plus"></i> Tambah
      </button>
      <a href="export-docx.php?id_sheet=<?= $id_sheet ?>" class="btn btn-primary btn-modern">
        <i class="fas fa-download"></i> Export
      </a>
    </div>

    <!-- Hamburger Menu -->
    <button class="hamburger" onclick="toggleMenu()">
      <span></span>
      <span></span>
      <span></span>
    </button>
  </div>

  <!-- Mobile Menu -->
  <div class="mobile-menu" id="mobileMenu">
    <a href="dashboard.php" class="btn btn-secondary btn-modern">
      <i class="fas fa-arrow-left"></i> Kembali
    </a>
    <button class="btn btn-success btn-modern" data-bs-toggle="modal" data-bs-target="#addTransactionModal" onclick="closeMenu()">
      <i class="fas fa-plus"></i> Tambah Transaksi
    </button>
    <a href="export-docx.php?id_sheet=<?= $id_sheet ?>" class="btn btn-primary btn-modern">
      <i class="fas fa-download"></i> Export Data
    </a>
  </div>
</nav>

<div class="container mt-4">
  <!-- Summary -->
  <div class="summary-container">
    <div class="summary-card">
      <div class="summary-value text-success">Rp <?= number_format($total_masuk, 0, ',', '.') ?></div>
      <div class="summary-label">Total Pemasukan</div>
    </div>
    <div class="summary-card">
      <div class="summary-value text-danger">Rp <?= number_format($total_keluar, 0, ',', '.') ?></div>
      <div class="summary-label">Total Pengeluaran</div>
    </div>
    <div class="summary-card">
      <div class="summary-value text-primary">Rp <?= number_format($saldo_akhir, 0, ',', '.') ?></div>
      <div class="summary-label">Saldo Akhir</div>
    </div>
  </div>

  <!-- Table -->
  <div class="table-card">
    <div class="table-header">
      <h5 class="table-title">Daftar Transaksi</h5>
      <div class="search-bar">
        <input type="text" class="form-control" placeholder="Cari transaksi...">
      </div>
    </div>

    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Keterangan</th>
            <th>Pemasukan</th>
            <th>Pengeluaran</th>
            <th>Saldo</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          while ($row = mysqli_fetch_assoc($q_data)):
          ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
            <td><?= htmlspecialchars($row['keterangan']) ?></td>
            <td class="text-success"><?= $row['pemasukan'] ? 'Rp ' . number_format($row['pemasukan'], 0, ',', '.') : '-' ?></td>
            <td class="text-danger"><?= $row['pengeluaran'] ? 'Rp ' . number_format($row['pengeluaran'], 0, ',', '.') : '-' ?></td>
            <td class="text-primary">Rp <?= number_format($row['saldo'], 0, ',', '.') ?></td>
            <td>
              <button 
                class="btn btn-sm btn-warning btn-edit" 
                data-id="<?= $row['id_data'] ?>"
                data-tanggal="<?= $row['tanggal'] ?>"
                data-keterangan="<?= htmlspecialchars($row['keterangan']) ?>"
                data-pemasukan="<?= $row['pemasukan'] ?>"
                data-pengeluaran="<?= $row['pengeluaran'] ?>"
              >
                <i class="fa fa-edit"></i>
              </button>
              <button 
                class="btn btn-sm btn-danger btn-delete"
                data-id="<?= $row['id_data'] ?>"
                data-keterangan="<?= htmlspecialchars($row['keterangan']) ?>"
              >
                <i class="fa fa-trash"></i>
              </button>
            </td>
          </tr>
          <?php endwhile; ?>

          <tr class="total-row">
            <td colspan="3" class="text-end">TOTAL:</td>
            <td class="text-success">Rp <?= number_format($total_masuk, 0, ',', '.') ?></td>
            <td class="text-danger">Rp <?= number_format($total_keluar, 0, ',', '.') ?></td>
            <td class="text-primary">Rp <?= number_format($saldo_akhir, 0, ',', '.') ?></td>
            <td></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Tambah Transaksi -->
<div class="modal fade" id="addTransactionModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h6 class="modal-title"><i class="fa fa-plus-circle me-2"></i>Tambah Transaksi</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form action="tambah-data.php" method="POST">
          <input type="hidden" name="id_sheet" value="<?= $id_sheet ?>">
          <div class="mb-3">
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Tipe Transaksi</label>
            <select class="form-select" name="tipe" required>
              <option value="">Pilih...</option>
              <option value="pemasukan">Pemasukan</option>
              <option value="pengeluaran">Pengeluaran</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <input type="text" name="keterangan" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Nominal (Rp)</label>
            <input type="number" name="nominal" class="form-control" required>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary btn-modern" type="button" data-bs-dismiss="modal">Batal</button>
            <button class="btn btn-success btn-modern" type="submit">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit Transaksi -->
<div class="modal fade" id="editTransactionModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h6 class="modal-title"><i class="fa fa-pen-to-square me-2"></i>Edit Transaksi</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="edit-data.php" method="POST">
        <div class="modal-body">
          <input type="hidden" name="id_data" id="edit_id_data">
          <input type="hidden" name="id_sheet" value="<?= $id_sheet ?>">

          <div class="mb-3">
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal" id="edit_tanggal" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Tipe Transaksi</label>
            <select class="form-select" name="tipe" id="edit_tipe" required>
              <option value="">Pilih...</option>
              <option value="pemasukan">Pemasukan</option>
              <option value="pengeluaran">Pengeluaran</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <input type="text" name="keterangan" id="edit_keterangan" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Nominal (Rp)</label>
            <input type="number" name="nominal" id="edit_nominal" class="form-control" required>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary btn-modern" type="button" data-bs-dismiss="modal">Batal</button>
          <button class="btn btn-warning btn-modern" type="submit">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Hapus Transaksi -->
<div class="modal fade" id="deleteTransactionModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger">
        <h6 class="modal-title"><i class="fa fa-triangle-exclamation me-2"></i>Konfirmasi Hapus</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="hapus-data.php" method="POST">
        <div class="modal-body">
          <input type="hidden" name="id_data" id="delete_id_data">
          <input type="hidden" name="id_sheet" value="<?= $id_sheet ?>">

          <p>Apakah Anda yakin ingin menghapus transaksi berikut?</p>
          <div class="alert alert-light border">
            <strong id="delete_keterangan"></strong>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger btn-modern">Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Hamburger Menu Toggle
function toggleMenu() {
  const hamburger = document.querySelector('.hamburger');
  const mobileMenu = document.getElementById('mobileMenu');
  
  hamburger.classList.toggle('active');
  mobileMenu.classList.toggle('active');
}

function closeMenu() {
  const hamburger = document.querySelector('.hamburger');
  const mobileMenu = document.getElementById('mobileMenu');
  
  hamburger.classList.remove('active');
  mobileMenu.classList.remove('active');
}

// Close menu when clicking outside
document.addEventListener('click', function(event) {
  const hamburger = document.querySelector('.hamburger');
  const mobileMenu = document.getElementById('mobileMenu');
  const navbar = document.querySelector('.navbar-custom');
  
  if (!navbar.contains(event.target)) {
    hamburger.classList.remove('active');
    mobileMenu.classList.remove('active');
  }
});

// Search functionality
document.querySelector('.search-bar input').addEventListener('input', function(e) {
  const term = e.target.value.toLowerCase();
  document.querySelectorAll('tbody tr').forEach(tr => {
    if (tr.classList.contains('total-row')) return;
    tr.style.display = tr.textContent.toLowerCase().includes(term) ? '' : 'none';
  });
});

// Edit Modal
document.querySelectorAll('.btn-edit').forEach(btn => {
  btn.addEventListener('click', () => {
    const id = btn.dataset.id;
    const tanggal = btn.dataset.tanggal.split(' ')[0];
    const keterangan = btn.dataset.keterangan;
    const pemasukan = parseInt(btn.dataset.pemasukan);
    const pengeluaran = parseInt(btn.dataset.pengeluaran);

    document.getElementById('edit_id_data').value = id;
    document.getElementById('edit_tanggal').value = tanggal;
    document.getElementById('edit_keterangan').value = keterangan;

    if (pemasukan > 0) {
      document.getElementById('edit_tipe').value = 'pemasukan';
      document.getElementById('edit_nominal').value = pemasukan;
    } else {
      document.getElementById('edit_tipe').value = 'pengeluaran';
      document.getElementById('edit_nominal').value = pengeluaran;
    }

    const modal = new bootstrap.Modal(document.getElementById('editTransactionModal'));
    modal.show();
  });
});

// Delete Modal
document.querySelectorAll('.btn-delete').forEach(btn => {
  btn.addEventListener('click', () => {
    const id = btn.dataset.id;
    const keterangan = btn.dataset.keterangan;

    document.getElementById('delete_id_data').value = id;
    document.getElementById('delete_keterangan').textContent = keterangan;

    const modal = new bootstrap.Modal(document.getElementById('deleteTransactionModal'));
    modal.show();
  });
});
</script>

</body>
</html>