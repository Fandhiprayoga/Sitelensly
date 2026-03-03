<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>Daftar Periode</h4>
        <div class="card-header-action">
          <?php if (activeGroupCan('periods.create')): ?>
          <a href="<?= base_url('admin/periods/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Periode
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
                <th>Nama Periode</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($periods)): ?>
              <tr>
                <td colspan="6" class="text-center text-muted">Belum ada data periode.</td>
              </tr>
              <?php else: ?>
              <?php $no = 1; foreach ($periods as $period): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><strong><?= esc($period['period_name']) ?></strong></td>
                <td><?= $period['start_date'] ? date('d M Y', strtotime($period['start_date'])) : '-' ?></td>
                <td><?= $period['end_date'] ? date('d M Y', strtotime($period['end_date'])) : '-' ?></td>
                <td>
                  <?php if ($period['status'] === 'open'): ?>
                    <span class="badge badge-success">Open</span>
                  <?php else: ?>
                    <span class="badge badge-danger">Closed</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (activeGroupCan('periods.edit')): ?>
                  <a href="<?= base_url('admin/periods/edit/' . $period['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                  <form action="<?= base_url('admin/periods/toggle-status/' . $period['id']) ?>" method="post" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-<?= $period['status'] === 'open' ? 'danger' : 'success' ?>" 
                            title="<?= $period['status'] === 'open' ? 'Tutup Periode' : 'Buka Periode' ?>"
                            onclick="return confirm('Yakin ingin <?= $period['status'] === 'open' ? 'menutup' : 'membuka' ?> periode ini?')">
                      <i class="fas fa-<?= $period['status'] === 'open' ? 'lock' : 'unlock' ?>"></i>
                    </button>
                  </form>
                  <?php endif; ?>
                  <?php if (activeGroupCan('periods.delete')): ?>
                  <form action="<?= base_url('admin/periods/delete/' . $period['id']) ?>" method="post" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus periode ini?')">
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
