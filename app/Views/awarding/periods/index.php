<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>Daftar Periode Awarding</h4>
        <div class="card-header-action">
          <?php if (activeGroupCan('awarding.periods.create')): ?>
          <a href="<?= base_url('admin/awarding/periods/create') ?>" class="btn btn-primary">
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
                <th>Periode Performansi</th>
                <th>Bobot</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($periods)): ?>
              <tr>
                <td colspan="6" class="text-center text-muted">Belum ada periode awarding.</td>
              </tr>
              <?php else: ?>
              <?php $no = 1; foreach ($periods as $period): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td>
                  <strong><?= esc($period['period_name']) ?></strong>
                  <?php if (!empty($period['description'])): ?>
                    <br><small class="text-muted"><?= esc(mb_substr($period['description'], 0, 60)) ?><?= mb_strlen($period['description']) > 60 ? '...' : '' ?></small>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (!empty($period['perf_period_name'])): ?>
                    <span class="badge badge-info"><?= esc($period['perf_period_name']) ?></span>
                  <?php else: ?>
                    <span class="text-muted">-</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (!empty($period['weight_complete'])): ?>
                    <span class="badge badge-success"><i class="fas fa-check"></i> Lengkap</span>
                  <?php else: ?>
                    <span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Belum Set</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php
                    $statusBadge = match($period['status']) {
                        'draft'     => 'badge-secondary',
                        'active'    => 'badge-success',
                        'completed' => 'badge-primary',
                        default     => 'badge-light',
                    };
                    $statusLabel = match($period['status']) {
                        'draft'     => 'Draft',
                        'active'    => 'Aktif',
                        'completed' => 'Selesai',
                        default     => $period['status'],
                    };
                  ?>
                  <span class="badge <?= $statusBadge ?>"><?= $statusLabel ?></span>
                </td>
                <td>
                  <!-- Status Actions -->
                  <?php if (activeGroupCan('awarding.periods.edit')): ?>
                    <?php if ($period['status'] === 'draft'): ?>
                    <form action="<?= base_url('admin/awarding/periods/set-status/' . $period['id']) ?>" method="post" class="d-inline">
                      <?= csrf_field() ?>
                      <input type="hidden" name="status" value="active">
                      <button type="submit" class="btn btn-sm btn-success" title="Aktifkan" onclick="return confirm('Aktifkan periode ini? Pastikan bobot sudah diset.')">
                        <i class="fas fa-play"></i>
                      </button>
                    </form>
                    <?php elseif ($period['status'] === 'active'): ?>
                    <form action="<?= base_url('admin/awarding/periods/set-status/' . $period['id']) ?>" method="post" class="d-inline">
                      <?= csrf_field() ?>
                      <input type="hidden" name="status" value="completed">
                      <button type="submit" class="btn btn-sm btn-primary" title="Selesaikan" onclick="return confirm('Selesaikan periode ini? Status tidak dapat dikembalikan.')">
                        <i class="fas fa-check-circle"></i>
                      </button>
                    </form>
                    <?php endif; ?>

                    <a href="<?= base_url('admin/awarding/periods/edit/' . $period['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                      <i class="fas fa-edit"></i>
                    </a>
                  <?php endif; ?>

                  <?php if (activeGroupCan('awarding.periods.delete') && $period['status'] !== 'active'): ?>
                  <form action="<?= base_url('admin/awarding/periods/delete/' . $period['id']) ?>" method="post" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus periode ini? Semua data terkait akan ikut terhapus.')">
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
