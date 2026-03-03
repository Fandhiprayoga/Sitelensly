<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>Daftar Website</h4>
        <div class="card-header-action">
          <?php if (activeGroupCan('websites.create')): ?>
          <a href="<?= base_url('admin/websites/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Website
          </a>
          <?php endif; ?>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>Nama Website</th>
                <th>Kategori</th>
                <th>URL</th>
                <th>Admin</th>
                <th>Kontak</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($websites)): ?>
              <tr>
                <td colspan="8" class="text-center text-muted">Belum ada data website.</td>
              </tr>
              <?php else: ?>
              <?php $no = 1; foreach ($websites as $website): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><strong><?= esc($website['website_name']) ?></strong></td>
                <td>
                  <span class="badge badge-light"><?= esc($categories[$website['category']] ?? ucfirst($website['category'])) ?></span>
                </td>
                <td>
                  <a href="<?= esc($website['url']) ?>" target="_blank" class="text-primary">
                    <?= esc($website['url']) ?> <i class="fas fa-external-link-alt fa-xs"></i>
                  </a>
                </td>
                <td><?= esc($website['admin_name'] ?? '-') ?></td>
                <td><?= esc($website['admin_contact'] ?? '-') ?></td>
                <td>
                  <?php if ($website['status'] === 'active'): ?>
                    <span class="badge badge-success">Aktif</span>
                  <?php else: ?>
                    <span class="badge badge-secondary">Nonaktif</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (activeGroupCan('websites.edit')): ?>
                  <a href="<?= base_url('admin/websites/edit/' . $website['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                  <?php endif; ?>
                  <?php if (activeGroupCan('websites.delete')): ?>
                  <form action="<?= base_url('admin/websites/delete/' . $website['id']) ?>" method="post" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus website ini?')">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
