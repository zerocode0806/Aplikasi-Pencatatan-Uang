<?php
include 'koneksi.php';

session_start();
if (!isset($_SESSION['user'])) {
  header('Location: index.php');
  exit;
}

// Hitung total sheet
$total_sheet = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM sheet_info"))['total'];

// Hitung total pemasukan, pengeluaran, saldo akhir
$sql_total = "SELECT 
    SUM(pemasukan) AS total_masuk, 
    SUM(pengeluaran) AS total_keluar, 
    (SUM(pemasukan) - SUM(pengeluaran)) AS saldo_akhir
    FROM sheet_data";
$total = mysqli_fetch_assoc(mysqli_query($conn, $sql_total));

// Ambil semua sheet beserta ringkasannya
$sql_sheets = "
  SELECT si.*, 
    COALESCE(SUM(sd.pemasukan), 0) AS total_masuk,
    COALESCE(SUM(sd.pengeluaran), 0) AS total_keluar,
    COALESCE(COUNT(sd.id_data), 0) AS total_transaksi
  FROM sheet_info si
  LEFT JOIN sheet_data sd ON si.id_sheet = sd.id_sheet
  GROUP BY si.id_sheet
  ORDER BY si.id_sheet DESC";
$sheets = mysqli_query($conn, $sql_sheets);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Aplikasi Management Kas</title>
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

    * { font-family: "Inter", sans-serif; }
    body { background: var(--bg); color: var(--text); margin: 0; padding: 0; }

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

    .stat-card {
      background: var(--card);
      border-radius: var(--radius);
      padding: 1.2rem;
      box-shadow: var(--shadow);
      text-align: center;
    }

    .stat-value { font-size: 1.6rem; font-weight: 700; }
    .stat-label { font-size: 0.85rem; color: var(--muted); }

    .sheet-card {
      background: var(--card);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      padding: 1.3rem;
      transition: 0.2s;
    }

    .sheet-card:hover { transform: translateY(-3px); }

    .sheet-header { display: flex; align-items: center; margin-bottom: 1rem; }
    .sheet-icon {
      width: 48px; height: 48px; border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      background: var(--primary); color: #fff; font-size: 1.3rem;
    }

    .sheet-title { font-weight: 600; font-size: 1.1rem; }
    .sheet-meta { font-size: 0.85rem; color: var(--muted); }

    .sheet-stats {
      display: flex; justify-content: space-between;
      font-size: 0.85rem; color: var(--muted);
    }

    .sheet-stat-value {
      display: block; font-weight: 700; font-size: 1rem; color: var(--text);
    }

    /* Responsive Mobile - Grid 2x2 untuk Statistics */
    @media (max-width: 768px) {
      /* Navbar Mobile */
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

      /* Force Grid 2x2 untuk Statistics Cards */
      .row.g-3.mb-4 {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 0.75rem !important;
      }

      .row.g-3.mb-4 .col-md-3 {
        width: 100% !important;
        max-width: 100% !important;
        flex: none !important;
      }

      /* Stat Card Mobile - SEIMBANG */
      .stat-card {
        padding: 1rem;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 120px;
      }

      .stat-card > div:first-child {
        margin-bottom: 0.5rem;
      }

      .stat-card i {
        font-size: 1.5rem;
      }

      .stat-value {
        font-size: 1.125rem;
        margin-bottom: 0.25rem;
        line-height: 1.3;
        word-break: break-word;
      }

      .stat-label {
        font-size: 0.75rem;
        line-height: 1.2;
      }

      /* Container padding mobile */
      .container.py-4 {
        padding-top: 1.5rem !important;
        padding-bottom: 1.5rem !important;
      }

      /* Modal Mobile */
      .modal-body.p-4 {
        padding: 1.25rem !important;
      }

      .modal-header {
        padding: 1.25rem 1.5rem;
      }

      .modal-footer {
        padding: 1rem 1.5rem;
      }
    }

    @media (max-width: 576px) {
      /* Extra small screens */
      .navbar-container {
        padding: 0 0.875rem;
      }

      .navbar-brand {
        font-size: 0.9375rem;
      }

      .navbar-brand i {
        font-size: 1rem;
      }

      /* Grid 2x2 tetap di extra small */
      .row.g-3.mb-4 {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 0.625rem !important;
      }

      .stat-card {
        padding: 0.875rem;
        min-height: 110px;
      }

      .stat-card i {
        font-size: 1.375rem;
      }

      .stat-value {
        font-size: 1rem;
        word-break: break-word;
      }

      .stat-label {
        font-size: 0.6875rem;
        line-height: 1.3;
      }

      /* Container padding extra small */
      .container.py-4 {
        padding-top: 1.25rem !important;
        padding-bottom: 1.25rem !important;
      }

      /* Modal Extra Small */
      .modal-body {
        padding: 1rem 1.25rem !important;
      }

      .modal-header {
        padding: 1rem 1.25rem;
      }

      .modal-footer {
        padding: 0.875rem 1.25rem;
      }

      .modal-title {
        font-size: 1rem;
      }

      .form-label {
        font-size: 0.8125rem;
      }

      .form-control, .form-select {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
      }
    }

    /* Medium screens (Tablet Portrait) */
    @media (min-width: 577px) and (max-width: 768px) {
      .row.g-3.mb-4 {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 0.875rem !important;
      }

      .stat-card {
        padding: 1.125rem;
        min-height: 130px;
      }

      .stat-card i {
        font-size: 1.625rem;
      }

      .stat-value {
        font-size: 1.25rem;
        line-height: 1.3;
      }

      .stat-label {
        font-size: 0.8125rem;
      }
    }

    /* Landscape Mobile */
    @media (max-width: 768px) and (orientation: landscape) {
      .row.g-3.mb-4 {
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 0.5rem !important;
      }

      .stat-card {
        padding: 0.75rem;
        min-height: 100px;
      }

      .stat-card i {
        font-size: 1.25rem;
      }

      .stat-value {
        font-size: 1rem;
      }

      .stat-label {
        font-size: 0.6875rem;
      }
    }

    /* Tablet adjustment (769px - 991px) */
    @media (min-width: 769px) and (max-width: 991px) {
      .navbar-container {
        padding: 0 1.5rem;
      }

      .stat-card {
        padding: 1.125rem;
      }

      .stat-value {
        font-size: 1.5rem;
      }

      .stat-label {
        font-size: 0.8125rem;
      }
    }
  </style>
</head>
<body>

<!-- Navbar dengan Hamburger Menu -->
<nav class="navbar-custom">
  <div class="navbar-container">
    <a href="#" class="navbar-brand">
      <i class="fas fa-wallet"></i>
      <span>Management Kas</span>
    </a>

    <!-- Desktop Menu -->
    <div class="navbar-menu">
      <button class="btn btn-success btn-modern" data-bs-toggle="modal" data-bs-target="#addSheetModal">
        <i class="fas fa-plus"></i> Sheet Baru
      </button>
      <a href="logout.php" class="btn btn-danger btn-modern" onclick="return confirm('Yakin ingin logout?')">
        <i class="fas fa-sign-out-alt"></i> Logout
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
    <button class="btn btn-success btn-modern" data-bs-toggle="modal" data-bs-target="#addSheetModal" onclick="closeMenu()">
      <i class="fas fa-plus"></i> Sheet Baru
    </button>
    <a href="logout.php" class="btn btn-danger btn-modern" onclick="return confirm('Yakin ingin logout?')">
      <i class="fas fa-sign-out-alt"></i> Logout
    </a>
  </div>
</nav>

<div class="container py-4">
  <!-- Statistik -->
  <div class="row g-3 mb-4">
    <div class="col-md-3"><div class="stat-card">
      <div><i class="fas fa-table text-primary"></i></div>
      <div class="stat-value"><?= $total_sheet ?></div>
      <div class="stat-label">Total Sheet</div>
    </div></div>

    <div class="col-md-3"><div class="stat-card">
      <div><i class="fas fa-arrow-trend-up text-success"></i></div>
      <div class="stat-value">Rp <?= number_format($total['total_masuk'] ?? 0, 0, ',', '.') ?></div>
      <div class="stat-label">Total Pemasukan</div>
    </div></div>

    <div class="col-md-3"><div class="stat-card">
      <div><i class="fas fa-arrow-trend-down text-danger"></i></div>
      <div class="stat-value">Rp <?= number_format($total['total_keluar'] ?? 0, 0, ',', '.') ?></div>
      <div class="stat-label">Total Pengeluaran</div>
    </div></div>

    <div class="col-md-3"><div class="stat-card">
      <div><i class="fas fa-wallet text-warning"></i></div>
      <div class="stat-value">Rp <?= number_format($total['saldo_akhir'] ?? 0, 0, ',', '.') ?></div>
      <div class="stat-label">Saldo Akhir</div>
    </div></div>
  </div>

  <!-- Daftar Sheet -->
  <div class="row g-3">
  <?php while ($row = mysqli_fetch_assoc($sheets)): ?>
    <div class="col-md-6 col-lg-4">
      <div class="sheet-card">
        <div class="sheet-header">
          <div class="sheet-icon"><i class="fas fa-file-invoice"></i></div>
          <div class="ms-3">
            <div class="sheet-title"><?= htmlspecialchars($row['nama']) ?></div>
            <div class="sheet-meta"><?= htmlspecialchars($row['kategori']) ?></div>
          </div>
        </div>
        <div class="sheet-stats">
          <div><span class="sheet-stat-value"><?= $row['total_transaksi'] ?></span> Transaksi</div>
          <div><span class="sheet-stat-value text-success">+<?= number_format($row['total_masuk'], 0, ',', '.') ?></span> Pemasukan</div>
          <div><span class="sheet-stat-value text-danger">-<?= number_format($row['total_keluar'], 0, ',', '.') ?></span> Pengeluaran</div>
        </div>
        <div class="d-flex gap-2 mt-3">
          <a href="detail-sheet.php?id_sheet=<?= $row['id_sheet'] ?>" class="btn btn-primary btn-modern flex-fill">
          <i class="fas fa-eye"></i> Buka
        </a>
          <button class="btn btn-warning btn-modern btn-edit"
            data-id="<?= $row['id_sheet'] ?>"
            data-nama="<?= htmlspecialchars($row['nama']) ?>"
            data-kategori="<?= htmlspecialchars($row['kategori']) ?>"
            data-saldo="<?= $row['saldo_awal'] ?>"
            data-deskripsi="<?= htmlspecialchars($row['deskripsi']) ?>"
          >
            <i class="fas fa-edit"></i>
          </button>
          <button class="btn btn-danger btn-modern btn-delete"
            data-id="<?= $row['id_sheet'] ?>"
            data-nama="<?= htmlspecialchars($row['nama']) ?>">
            <i class="fas fa-trash"></i>
          </button>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>

</div>

<!-- Modal Tambah Sheet -->
<div class="modal fade" id="addSheetModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Buat Sheet Baru</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <form method="POST" action="tambah-sheet.php">
          <div class="mb-3">
            <label class="form-label">Nama Sheet</label>
            <input type="text" class="form-control" name="nama" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select class="form-select" name="kategori" required>
              <option value="">Pilih Kategori...</option>
              <option>Bisnis</option>
              <option>Organisasi</option>
              <option>Pribadi</option>
              <option>Proyek</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Saldo Awal</label>
            <input type="number" class="form-control" name="saldo_awal" value="0">
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea class="form-control" name="deskripsi" rows="2"></textarea>
          </div>
          <div class="text-end">
            <button type="submit" class="btn btn-success btn-modern">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit Sheet -->
<div class="modal fade" id="editSheetModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-sm rounded-3">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title"><i class="fas fa-pen me-2"></i>Edit Sheet</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="edit-sheet.php" method="POST">
        <div class="modal-body">
          <input type="hidden" name="id_sheet" id="edit_id_sheet">

          <div class="mb-3">
            <label class="form-label">Nama Sheet</label>
            <input type="text" name="nama" id="edit_nama" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Kategori</label>
            <input type="text" name="kategori" id="edit_kategori" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Saldo Awal</label>
            <input type="number" name="saldo_awal" id="edit_saldo" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="2"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning text-white">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Hapus Sheet -->
<div class="modal fade" id="deleteSheetModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-sm rounded-3">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="fas fa-trash-alt me-2"></i>Hapus Sheet</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="hapus-sheet.php" method="POST">
        <div class="modal-body text-center">
          <input type="hidden" name="id_sheet" id="delete_id_sheet">
          <p class="mb-0 fs-5">Apakah kamu yakin ingin menghapus sheet:</p>
          <p class="fw-bold text-danger mt-2" id="delete_nama_sheet">Sheet Name</p>
          <p class="text-muted small">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Hapus</button>
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

// Edit Sheet Modal
document.querySelectorAll('.btn-edit').forEach(btn => {
  btn.addEventListener('click', () => {
    document.getElementById('edit_id_sheet').value = btn.dataset.id;
    document.getElementById('edit_nama').value = btn.dataset.nama;
    document.getElementById('edit_kategori').value = btn.dataset.kategori;
    document.getElementById('edit_saldo').value = btn.dataset.saldo;
    document.getElementById('edit_deskripsi').value = btn.dataset.deskripsi;

    const modal = new bootstrap.Modal(document.getElementById('editSheetModal'));
    modal.show();
  });
});

// Delete Sheet Modal
document.querySelectorAll('.btn-delete').forEach(btn => {
  btn.addEventListener('click', () => {
    document.getElementById('delete_id_sheet').value = btn.dataset.id;
    document.getElementById('delete_nama_sheet').textContent = btn.dataset.nama;

    const modal = new bootstrap.Modal(document.getElementById('deleteSheetModal'));
    modal.show();
  });
});
</script>

</body>
</html>