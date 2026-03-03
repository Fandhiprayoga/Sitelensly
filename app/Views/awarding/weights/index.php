<div class="row">
  <div class="col-12 col-md-8 offset-md-2">
    <div class="card">
      <div class="card-header">
        <h4>Bobot Penilaian Awarding</h4>
      </div>
      <div class="card-body">
        <!-- Pilih Periode -->
        <form method="get" class="mb-4">
          <div class="form-row align-items-end">
            <div class="form-group col-md-6 mb-0">
              <label for="period_id"><strong>Periode Awarding</strong></label>
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

        <?php if ($selectedPeriod && $selectedPeriod['status'] === 'completed'): ?>
        <div class="alert alert-info">
          <i class="fas fa-info-circle"></i> Periode ini sudah <strong>Selesai</strong>. Bobot tidak dapat diubah.
        </div>
        <?php endif; ?>

        <?php if ($selectedPeriodId): ?>
        <div class="alert alert-light border">
          <h6 class="mb-2"><i class="fas fa-info-circle text-primary"></i> Metode SAW (Simple Additive Weighting)</h6>
          <p class="mb-1">Total bobot ketiga kriteria harus sama dengan <strong>1.00</strong></p>
          <p class="mb-0">Contoh: Analytics = 0.40, Konten = 0.30, Standarisasi Web = 0.30</p>
        </div>

        <form action="<?= base_url('admin/awarding/weights/store') ?>" method="post">
          <?= csrf_field() ?>
          <input type="hidden" name="awarding_period_id" value="<?= $selectedPeriodId ?>">

          <div class="table-responsive">
            <table class="table table-bordered">
              <thead class="thead-light">
                <tr>
                  <th>Kode Kriteria</th>
                  <th>Nama Kriteria</th>
                  <th>Tipe</th>
                  <th style="width: 200px;">Bobot (W)</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($criteria as $code => $meta): ?>
                <?php $existingWeight = $weights[$code] ?? null; ?>
                <tr>
                  <td>
                    <code><?= esc($code) ?></code>
                  </td>
                  <td>
                    <strong><?= esc($meta['name']) ?></strong>
                    <?php if ($code === 'analytics'): ?>
                      <br><small class="text-muted">Jumlah klik: Desktop + Mobile + Tablet</small>
                    <?php elseif ($code === 'content'): ?>
                      <br><small class="text-muted">Jumlah postingan di website</small>
                    <?php elseif ($code === 'web_standardization'): ?>
                      <br><small class="text-muted">Kelengkapan 17 elemen standar website</small>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="badge badge-<?= $meta['type'] === 'benefit' ? 'success' : 'danger' ?>">
                      <?= ucfirst($meta['type']) ?>
                    </span>
                  </td>
                  <td>
                    <input type="number" class="form-control weight-input" name="weights[<?= $code ?>]"
                           value="<?= old('weights.' . $code, $existingWeight['weight_value'] ?? '') ?>"
                           step="0.0001" min="0.0001" max="0.9999" required
                           <?= ($selectedPeriod && $selectedPeriod['status'] === 'completed') ? 'disabled' : '' ?>>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot>
                <tr class="table-info">
                  <td colspan="3" class="text-right"><strong>Total Bobot:</strong></td>
                  <td>
                    <strong id="totalWeight">0.0000</strong>
                    <span id="weightStatus" class="ml-2"></span>
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>

          <?php if (!$selectedPeriod || $selectedPeriod['status'] !== 'completed'): ?>
          <div class="form-group text-right">
            <button type="submit" class="btn btn-primary" id="btnSaveWeight">
              <i class="fas fa-save"></i> Simpan Bobot
            </button>
          </div>
          <?php endif; ?>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?= $this->section('page_js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  function updateTotal() {
    let total = 0;
    document.querySelectorAll('.weight-input').forEach(function(input) {
      total += parseFloat(input.value) || 0;
    });
    document.getElementById('totalWeight').textContent = total.toFixed(4);

    const status = document.getElementById('weightStatus');
    const btn = document.getElementById('btnSaveWeight');
    if (Math.abs(total - 1.0) < 0.001) {
      status.innerHTML = '<span class="badge badge-success"><i class="fas fa-check"></i> Valid</span>';
      if (btn) btn.disabled = false;
    } else {
      status.innerHTML = '<span class="badge badge-danger"><i class="fas fa-times"></i> Harus = 1.0000</span>';
      if (btn) btn.disabled = true;
    }
  }

  document.querySelectorAll('.weight-input').forEach(function(input) {
    input.addEventListener('input', updateTotal);
  });

  updateTotal();
});
</script>
<?= $this->endSection() ?>
