<?php
$currentUser = auth()->user();
$currentUrl  = uri_string();

/**
 * Helper untuk cek apakah menu aktif
 */
function isMenuActive(string $path): string {
    $currentUrl = uri_string();
    return (strpos($currentUrl, $path) !== false) ? 'active' : '';
}

function isDropdownActive(array $paths): string {
    $currentUrl = uri_string();
    foreach ($paths as $path) {
        if (strpos($currentUrl, $path) !== false) {
            return 'active';
        }
    }
    return '';
}
?>
<div class="main-sidebar sidebar-style-1">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand">
      <a href="<?= base_url('dashboard') ?>"><?= esc(setting('App.siteName') ?? 'CI4 RBAC') ?></a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <a href="<?= base_url('dashboard') ?>"><?= esc(setting('App.siteNameShort') ?? 'C4') ?></a>
    </div>
    <ul class="sidebar-menu">

      <!-- Dashboard -->
      <li class="menu-header">Dashboard</li>
      <li class="<?= isMenuActive('dashboard') && !str_contains($currentUrl, 'admin') && !str_contains($currentUrl, 'performance-dashboard') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('dashboard') ?>"><i class="fas fa-fire"></i> <span>Dashboard</span></a>
      </li>

      <!-- Dashboard Performansi (semua user yang punya permission) -->
      <?php if (activeGroupCan('performance.dashboard')): ?>
      <li class="<?= isMenuActive('performance-dashboard') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('performance-dashboard') ?>"><i class="fas fa-chart-line"></i> <span>Dashboard Performansi</span></a>
      </li>
      <?php endif; ?>

      <!-- Hasil Awarding (semua user yang punya permission) -->
      <?php if (activeGroupCan('awarding.results.view') && !activeGroupCan('admin.access')): ?>
      <li class="<?= isMenuActive('awarding-results') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('awarding-results') ?>"><i class="fas fa-trophy"></i> <span>Hasil Awarding</span></a>
      </li>
      <?php endif; ?>

      <!-- Admin Menu (hanya untuk active group yang punya akses admin) -->
      <?php if (activeGroupCan('admin.access')): ?>
      <li class="menu-header">Administrasi</li>

      <!-- User Management -->
      <?php if (activeGroupCan('users.list')): ?>
      <li class="<?= isMenuActive('admin/users') ?>">
        <a class="nav-link" href="<?= base_url('admin/users') ?>"><i class="fas fa-users"></i> <span>Manajemen User</span></a>
      </li>
      <?php endif; ?>

      <!-- Role Management (superadmin only) -->
      <?php if (activeGroupIs('superadmin')): ?>
      <li class="nav-item dropdown <?= isDropdownActive(['admin/roles']) ?>">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-user-shield"></i> <span>Role & Permission</span></a>
        <ul class="dropdown-menu">
          <li class="<?= isMenuActive('admin/roles') && !str_contains($currentUrl, 'permissions') ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('admin/roles') ?>">Daftar Role</a>
          </li>
          <li class="<?= isMenuActive('admin/roles/permissions') ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('admin/roles/permissions') ?>">Permission Matrix</a>
          </li>
        </ul>
      </li>
      <?php endif; ?>

      <!-- Settings -->
      <?php if (activeGroupCan('admin.settings')): ?>
      <li class="<?= isMenuActive('admin/settings') ?>">
        <a class="nav-link" href="<?= base_url('admin/settings') ?>"><i class="fas fa-cog"></i> <span>Pengaturan</span></a>
      </li>
      <?php endif; ?>

      <!-- Performansi Website Menu -->
      <li class="menu-header">Performansi Website</li>

      <!-- Manajemen Periode -->
      <?php if (activeGroupCan('periods.list')): ?>
      <li class="<?= isMenuActive('admin/periods') ?>">
        <a class="nav-link" href="<?= base_url('admin/periods') ?>"><i class="fas fa-calendar-alt"></i> <span>Manajemen Periode</span></a>
      </li>
      <?php endif; ?>

      <!-- Master Website -->
      <?php if (activeGroupCan('websites.list')): ?>
      <li class="<?= isMenuActive('admin/websites') ?>">
        <a class="nav-link" href="<?= base_url('admin/websites') ?>"><i class="fas fa-globe"></i> <span>Master Website</span></a>
      </li>
      <?php endif; ?>

      <!-- Input Data Performansi -->
      <?php if (activeGroupCan('performance.input')): ?>
      <li class="<?= isMenuActive('admin/performance') ?>">
        <a class="nav-link" href="<?= base_url('admin/performance') ?>"><i class="fas fa-keyboard"></i> <span>Input Data Performansi</span></a>
      </li>
      <?php endif; ?>

      <!-- Laporan Ringkas -->
      <?php if (activeGroupCan('reports.view')): ?>
      <li class="<?= isMenuActive('admin/reports') ?>">
        <a class="nav-link" href="<?= base_url('admin/reports') ?>"><i class="fas fa-file-alt"></i> <span>Laporan Ringkas</span></a>
      </li>
      <?php endif; ?>

      <!-- Awarding -->
      <?php if (activeGroupCan('awarding.periods.list') || activeGroupCan('awarding.weights.manage') || activeGroupCan('awarding.scores.list') || activeGroupCan('awarding.results.view')): ?>
      <li class="menu-header">Awarding</li>

      <?php if (activeGroupCan('awarding.periods.list')): ?>
      <li class="<?= isMenuActive('admin/awarding/periods') ?>">
        <a class="nav-link" href="<?= base_url('admin/awarding/periods') ?>"><i class="fas fa-calendar-check"></i> <span>Periode Awarding</span></a>
      </li>
      <?php endif; ?>

      <?php if (activeGroupCan('awarding.weights.manage')): ?>
      <li class="<?= isMenuActive('admin/awarding/weights') ?>">
        <a class="nav-link" href="<?= base_url('admin/awarding/weights') ?>"><i class="fas fa-balance-scale"></i> <span>Bobot Penilaian</span></a>
      </li>
      <?php endif; ?>

      <?php if (activeGroupCan('awarding.scores.list')): ?>
      <li class="<?= isMenuActive('admin/awarding/scores') ?>">
        <a class="nav-link" href="<?= base_url('admin/awarding/scores') ?>"><i class="fas fa-clipboard-check"></i> <span>Input Penilaian</span></a>
      </li>
      <?php endif; ?>

      <?php if (activeGroupCan('awarding.results.view')): ?>
      <li class="<?= isMenuActive('admin/awarding/results') ?>">
        <a class="nav-link" href="<?= base_url('admin/awarding/results') ?>"><i class="fas fa-trophy"></i> <span>Hasil & Peringkat</span></a>
      </li>
      <?php endif; ?>

      <?php endif; ?>

      <?php endif; ?>

      <!-- Profil -->
      <li class="menu-header">Akun</li>
      <li class="<?= isMenuActive('profile') ?>">
        <a class="nav-link" href="<?= base_url('profile') ?>"><i class="far fa-user"></i> <span>Profil Saya</span></a>
      </li>
      <li>
        <a class="nav-link text-danger" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
      </li>

    </ul>
  </aside>
</div>
