<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>Daftar Penilaian Awarding</h4>
        <div class="card-header-action">
          <?php if (activeGroupCan('awarding.scores.input') && $selectedPeriod && $selectedPeriod['status'] === 'active'): ?>
          <a href="<?= base_url('admin/awarding/scores/create?period_id=' . $selectedPeriodId) ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Input Penilaian
          </a>
          <?php endif; ?>
        </div>
      </div>
      <div class="card-body">
        <!-- Pilih Periode -->
        <form method="get" class="mb-4">
          <div class="form-row align-items-end">
            <div class="form-group col-md-4 mb-0">
              <label for="period_id">Periode Awarding</label>
              <select class="form-control" id="period_id" name="period_id" onchange="this.form.submit()">
                <?php if (empty($periods)): ?>
                <option value="">-- Belum ada periode --</option>
                <?php endif; ?>
                <?php foreach ($periods as $period): ?>
                <option value="<?= $period['id'] ?>" <?= $selectedPeriodId == $period['id'] ? 'selected' : '' ?>>
                  <?= esc($period['period_name']) ?>
                  (<?= match($period['status']) { 'draft' => 'Draft', 'active' => 'Aktif', 'completed' => 'Selesai', default => $period['status'] } ?>)
                </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </form>

        <?php if ($selectedPeriod && $selectedPeriod['status'] === 'draft'): ?>
        <div class="alert alert-warning">
          <i class="fas fa-exclamation-triangle"></i> Periode ini masih berstatus <strong>Draft</strong>. 
          Aktifkan periode terlebih dahulu sebelum mulai input penilaian.
        </div>
        <?php endif; ?>

        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>Nama Website</th>
                <th class="text-center">Analytics<br><small>(Total Klik)</small></th>
                <th class="text-center">Konten<br><small>(Post)</small></th>
                <th class="text-center">Standarisasi<br><small>(Elemen)</small></th>
                <th class="text-center">Sumber</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($scores)): ?>
              <tr>
                <td colspan="7" class="text-center text-muted">Belum ada data penilaian untuk periode ini.</td>
              </tr>
              <?php else: ?>
              <?php $no = 1; foreach ($scores as $score): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td>
                  <strong><?= esc($score['website_name']) ?></strong>
                  <?php if (!empty($score['website_category'])): ?>
                    <span class="badge badge-light"><?= esc(ucfirst($score['website_category'])) ?></span>
                  <?php endif; ?>
                  <br><small class="text-muted"><?= esc($score['url']) ?></small>
                </td>
                <td class="text-center">
                  <strong class="text-primary"><?= number_format($score['calc_analytics']) ?></strong>
                  <br><small class="text-muted">
                    W:<?= number_format($score['clicks_web']) ?>
                    M:<?= number_format($score['clicks_mobile']) ?>
                    T:<?= number_format($score['clicks_tablet']) ?>
                  </small>
                </td>
                <td class="text-center">
                  <strong class="text-success"><?= number_format($score['calc_content']) ?></strong>
                </td>
                <td class="text-center">
                  <strong class="text-warning"><?= $score['calc_standard'] ?>/17</strong>
                  <!-- Detail Button -->
                  <button type="button" class="btn btn-xs btn-outline-info ml-1 btn-std-modal" data-id="<?= $score['id'] ?>" title="Detail Elemen">
                    <i class="fas fa-eye"></i>
                  </button>
                </td>
                <td class="text-center">
                  <?php if ($score['is_imported']): ?>
                    <span class="badge badge-info"><i class="fas fa-download"></i> Import</span>
                  <?php else: ?>
                    <span class="badge badge-secondary"><i class="fas fa-keyboard"></i> Manual</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (activeGroupCan('awarding.scores.edit') && $selectedPeriod && $selectedPeriod['status'] === 'active'): ?>
                  <a href="<?= base_url('admin/awarding/scores/edit/' . $score['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                  <?php endif; ?>
                  <?php if (activeGroupCan('awarding.scores.delete') && $selectedPeriod && $selectedPeriod['status'] === 'active'): ?>
                  <form action="<?= base_url('admin/awarding/scores/delete/' . $score['id']) ?>" method="post" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus penilaian ini?')">
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

<!-- Modals detail standarisasi -->
<?php if (!empty($scores)): ?>
<?php foreach ($scores as $score): ?>
<div class="modal fade" id="stdModal<?= $score['id'] ?>" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Standarisasi Web - <?= esc($score['website_name']) ?></h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <table class="table table-sm table-bordered">
          <thead class="thead-light">
            <tr>
              <th>#</th>
              <th>Elemen</th>
              <th class="text-center">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php $elNo = 1; foreach ($elements as $field => $label): ?>
            <tr>
              <td><?= $elNo++ ?></td>
              <td><?= esc($label) ?></td>
              <td class="text-center">
                <?php if (!empty($score[$field])): ?>
                  <span class="badge badge-success"><i class="fas fa-check"></i> Ada</span>
                <?php else: ?>
                  <span class="badge badge-danger"><i class="fas fa-times"></i> Tidak</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Move modals to body to avoid stacking context / overflow issues
    document.querySelectorAll('.modal').forEach(function(modal) {
        document.body.appendChild(modal);
    });

    // Explicit modal trigger (bypass Stisla data-api interference)
    $(document).on('click', '.btn-std-modal', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        $('#stdModal' + id).modal('show');
    });
});
</script>
