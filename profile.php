<?php
include 'koneksi.php';

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$id_user = $_SESSION['user']['id_user'];

$query = mysqli_query(
    $conn,
    "SELECT * FROM user WHERE id_user='$id_user'"
);

$user = mysqli_fetch_assoc($query);

if (!$user) {
    die("Data user tidak ditemukan");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Profile User</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
    --primary: #4f46e5;
    --primary-hover: #4338ca;
    --success: #22c55e;
    --warning: #f59e0b;
    --warning-hover: #d97706;
    --danger: #ef4444;
    --text: #1e293b;
    --muted: #64748b;
    --bg: #f8fafc;
    --card: #ffffff;
    --radius: 16px;
    --radius-sm: 10px;
    --shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05), 0 2px 6px -1px rgba(0, 0, 0, 0.03);
}

* {
    font-family: 'Inter', sans-serif;
}

body {
    background: var(--bg);
    color: var(--text);
    -webkit-font-smoothing: antialiased;
}

/* Navbar Modern */
.navbar-custom {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(226, 232, 240, 0.8);
    padding: 0.85rem 0;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.navbar-container {
    max-width: 1200px;
    margin: auto;
    padding: 0 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.navbar-brand {
    text-decoration: none;
    color: var(--primary);
    font-weight: 700;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Tombol Modern */
.btn-modern {
    border-radius: var(--radius-sm);
    border: none;
    font-weight: 600;
    font-size: 0.9rem;
    padding: 0.55rem 1.1rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.25s ease;
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Card Container */
.sheet-card {
    background: var(--card);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 2.5rem;
    border: 1px solid rgba(226, 232, 240, 0.6);
}

/* Stat Card (Informasi Detail User) */
.stat-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: var(--radius-sm);
    padding: 1.2rem;
    text-align: center;
    transition: all 0.2s ease;
}

.stat-card:hover {
    border-color: var(--primary);
    background: #ffffff;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.05);
}

.stat-value {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text);
    margin-top: 0.25rem;
}

.stat-label {
    color: var(--muted);
    font-size: 0.8rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Avatar */
.avatar {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), #818cf8);
    color: white;
    font-size: 38px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: auto;
    box-shadow: 0 8px 20px rgba(79, 70, 229, 0.25);
}

/* Modal Customization */
.modal-content {
    border-radius: var(--radius);
    border: none;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.modal-header {
    border-top-left-radius: var(--radius);
    border-top-right-radius: var(--radius);
    padding: 1.25rem 1.5rem;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #f1f5f9;
}

.form-control {
    border-radius: var(--radius-sm);
    padding: 0.65rem 1rem;
    border-color: #cbd5e1;
}

.form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
}

/* Responsive Tablet & Mobile */
@media (max-width: 768px) {
    .navbar-container {
        padding: 0 1rem;
    }

    .navbar-brand {
        font-size: 1rem;
    }

    .sheet-card {
        padding: 1.5rem;
    }

    .avatar {
        width: 90px;
        height: 90px;
        font-size: 30px;
    }

    .sheet-card h3 {
        font-size: 1.25rem;
    }

    .stat-card {
        padding: 1rem;
        min-height: 105px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .stat-card i {
        font-size: 1.2rem;
    }

    .stat-value {
        font-size: 0.9rem;
        word-break: break-word;
        line-height: 1.3;
    }

    .stat-label {
        font-size: 0.7rem;
    }

    /* Membuat tombol aksi profil tersusun secara vertikal penuh di mobile agar nyaman ditekan */
    .action-buttons-container {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .btn-modern {
        width: 100%;
        justify-content: center;
    }
}
</style>
</head>
<body>

<nav class="navbar-custom">
    <div class="navbar-container">
        <a href="dashboard.php" class="navbar-brand">
            <i class="fas fa-wallet"></i>
            Management Kas
        </a>

        <div class="d-flex gap-2">
            <a href="dashboard.php" class="btn btn-primary btn-modern">
                <i class="fas fa-home"></i>
                <span class="d-none d-sm-inline">Dashboard</span>
            </a>

            <a href="logout.php"
               class="btn btn-danger btn-modern"
               onclick="return confirm('Yakin ingin logout?')">
                <i class="fas fa-sign-out-alt"></i>
                <span class="d-none d-sm-inline">Logout</span>
            </a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="sheet-card">
                
                <div class="text-center">
                    <div class="avatar">
                        <?= strtoupper(substr($user['nama'], 0, 1)) ?>
                    </div>

                    <h3 class="mt-3 mb-1 fw-bold">
                        <?= htmlspecialchars($user['nama']) ?>
                    </h3>

                    <span class="badge bg-primary px-3 py-2 rounded-pill fw-medium">
                        <?= htmlspecialchars($user['level']) ?>
                    </span>
                </div>

                <hr class="my-4 text-muted opacity-25">

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <i class="fas fa-user text-primary mb-1 fs-5"></i>
                            <div class="stat-value text-truncate">
                                <?= htmlspecialchars($user['nama']) ?>
                            </div>
                            <div class="stat-label">Nama Lengkap</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="stat-card">
                            <i class="fas fa-at text-success mb-1 fs-5"></i>
                            <div class="stat-value text-truncate">
                                <?= htmlspecialchars($user['usernama']) ?>
                            </div>
                            <div class="stat-label">Username</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="stat-card">
                            <i class="fas fa-shield-halved text-warning mb-1 fs-5"></i>
                            <div class="stat-value text-truncate">
                                <?= htmlspecialchars($user['level']) ?>
                            </div>
                            <div class="stat-label">Level User</div>
                        </div>
                    </div>
                </div>

                <!-- Bagian Tombol Aksi Profil -->
                <div class="action-buttons-container text-center mt-4 d-flex justify-content-center gap-2 flex-wrap">
                    <button class="btn btn-warning btn-modern text-white"
                            data-bs-toggle="modal"
                            data-bs-target="#editProfileModal">
                        <i class="fas fa-user-edit"></i> Edit Profil
                    </button>

                    <button class="btn btn-primary btn-modern"
                            data-bs-toggle="modal"
                            data-bs-target="#passwordModal">
                        <i class="fas fa-lock"></i> Ubah Password
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Profil -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title fw-semibold">
                    <i class="fas fa-user-edit me-2"></i> Edit Profil
                </h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="update_profile.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_user" value="<?= $user['id_user'] ?>">

                    <div class="mb-3">
                        <label class="form-label fw-medium">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Username</label>
                        <input type="text" class="form-control" name="usernama" value="<?= htmlspecialchars($user['usernama']) ?>" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-modern text-white">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Password -->
<div class="modal header fade" id="passwordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-semibold">
                    <i class="fas fa-lock me-2"></i> Ubah Password
                </h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="update_password.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_user" value="<?= $user['id_user'] ?>">

                    <div class="mb-3">
                        <label class="form-label fw-medium">Password Baru</label>
                        <input type="password" class="form-control" name="password" placeholder="Masukkan password baru" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-modern">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>