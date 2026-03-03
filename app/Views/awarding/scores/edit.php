<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>Edit Penilaian Website</h4>
        <div class="card-header-action">
          <span class="badge badge-success"><?= esc($selectedPeriod['period_name']) ?></span>
        </div>
      </div>
      <div class="card-body">
        <form action="<?= base_url('admin/awarding/scores/update/' . $score['id']) ?>" method="post" id="scoreForm">
          <?= csrf_field() ?>

          <!-- Website (readonly) -->
          <div class="form-group">
            <label>Website</label>
            <input type="text" class="form-control" value="<?= esc($website['website_name']) ?> [<?= ucfirst($website['category'] ?? '') ?>]" disabled>
          </div>

          <!-- Import dari Performansi -->
          <div class="card bg-light mb-4">
            <div class="card-body">
              <h6 class="text-primary"><i class="fas fa-download"></i> Import dari Modul Performansi (Opsional)</h6>
              <p class="text-muted mb-3">Ambil data analytics & konten otomatis dari data performansi yang sudah diinput.</p>
              <div class="form-row align-items-end">
                <div class="form-group col-md-6 mb-0">
                  <label for="perf_period_id">Pilih Periode Performansi</label>
                  <select class="form-control" id="perf_period_id">
                    <option value="">-- Tidak import --</option>
                    <?php foreach ($perfPeriods as $pp): ?>
                    <option value="<?= $pp['id'] ?>"
                      <?= (!empty($selectedPeriod['performance_period_id']) && $selectedPeriod['performance_period_id'] == $pp['id']) ? 'selected' : '' ?>>
                      <?= esc($pp['period_name']) ?> <?= $pp['status'] === 'closed' ? '(Closed)' : '(Open)' ?>
                    </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group col-md-3 mb-0">
                  <button type="button" class="btn btn-info" id="btnImport">
                    <i class="fas fa-sync-alt"></i> Ambil Data
                  </button>
                </div>
                <div class="form-group col-md-3 mb-0">
                  <span id="importStatus"></span>
                </div>
              </div>
            </div>
          </div>

          <!-- Kriteria 1: Analytics -->
          <hr>
          <h5 class="text-primary mb-3"><i class="fas fa-mouse-pointer"></i> Kriteria 1: Analytics (Jumlah Klik)</h5>
          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="clicks_web">Klik Desktop <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="clicks_web" name="clicks_web"
                     value="<?= old('clicks_web', $score['clicks_web']) ?>" min="0" required>
            </div>
            <div class="form-group col-md-4">
              <label for="clicks_mobile">Klik Mobile <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="clicks_mobile" name="clicks_mobile"
                     value="<?= old('clicks_mobile', $score['clicks_mobile']) ?>" min="0" required>
            </div>
            <div class="form-group col-md-4">
              <label for="clicks_tablet">Klik Tablet <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="clicks_tablet" name="clicks_tablet"
                     value="<?= old('clicks_tablet', $score['clicks_tablet']) ?>" min="0" required>
            </div>
          </div>
          <div class="alert alert-light border py-2">
            <strong>Total Klik: <span id="totalClicks" class="text-primary">0</span></strong>
          </div>

          <!-- Kriteria 2: Konten -->
          <hr>
          <h5 class="text-success mb-3"><i class="fas fa-file-alt"></i> Kriteria 2: Konten (Jumlah Postingan)</h5>
          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="total_posts">Jumlah Postingan <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="total_posts" name="total_posts"
                     value="<?= old('total_posts', $score['total_posts']) ?>" min="0" required>
            </div>
          </div>

          <!-- Kriteria 3: Standarisasi Web -->
          <hr>
          <h5 class="text-warning mb-3"><i class="fas fa-check-square"></i> Kriteria 3: Standarisasi Web (17 Elemen)</h5>
          <p class="text-muted">Centang elemen yang tersedia/sesuai standar di website.</p>

          <div class="row">
            <?php foreach ($elements as $field => $label): ?>
            <div class="col-md-4 col-sm-6">
              <div class="custom-control custom-checkbox mb-2">
                <input type="checkbox" class="custom-control-input std-checkbox" id="<?= $field ?>" name="<?= $field ?>" value="1"
                       <?= old($field, $score[$field]) ? 'checked' : '' ?>>
                <label class="custom-control-label" for="<?= $field ?>"><?= esc($label) ?></label>
              </div>
            </div>
            <?php endforeach; ?>
          </div>

          <div class="alert alert-light border py-2 mt-2">
            <strong>Elemen Terpenuhi: <span id="stdCount" class="text-warning">0</span>/17</strong>
            <button type="button" class="btn btn-xs btn-outline-success ml-3" id="btnCheckAll">Centang Semua</button>
            <button type="button" class="btn btn-xs btn-outline-secondary ml-1" id="btnUncheckAll">Hapus Semua</button>
          </div>

          <input type="hidden" name="is_imported" id="is_imported" value="<?= $score['is_imported'] ?>">

          <!-- Notes -->
          <hr>
          <div class="form-group">
            <label for="notes">Catatan</label>
            <textarea class="form-control" id="notes" name="notes" rows="2"><?= old('notes', $score['notes']) ?></textarea>
          </div>

          <div class="form-group text-right">
            <a href="<?= base_url('admin/awarding/scores?period_id=' . $score['awarding_period_id']) ?>" class="btn btn-secondary mr-1">Batal</a>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Simpan Perubahan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?= $this->section('page_js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // --- Total Klik Calculator ---
  function updateTotalClicks() {
    const web = parseInt(document.getElementById('clicks_web').value) || 0;
    const mobile = parseInt(document.getElementById('clicks_mobile').value) || 0;
    const tablet = parseInt(document.getElementById('clicks_tablet').value) || 0;
    document.getElementById('totalClicks').textContent = (web + mobile + tablet).toLocaleString();
  }
  ['clicks_web', 'clicks_mobile', 'clicks_tablet'].forEach(function(id) {
    document.getElementById(id).addEventListener('input', updateTotalClicks);
  });
  updateTotalClicks();

  // --- Standardization Counter ---
  function updateStdCount() {
    const checked = document.querySelectorAll('.std-checkbox:checked').length;
    document.getElementById('stdCount').textContent = checked;
  }
  document.querySelectorAll('.std-checkbox').forEach(function(cb) {
    cb.addEventListener('change', updateStdCount);
  });
  updateStdCount();

  document.getElementById('btnCheckAll').addEventListener('click', function() {
    document.querySelectorAll('.std-checkbox').forEach(function(cb) { cb.checked = true; });
    updateStdCount();
  });
  document.getElementById('btnUncheckAll').addEventListener('click', function() {
    document.querySelectorAll('.std-checkbox').forEach(function(cb) { cb.checked = false; });
    updateStdCount();
  });

  // --- Import Button Logic ---
  const websiteId = '<?= $score['website_id'] ?>';
  const perfPeriodSelect = document.getElementById('perf_period_id');
  const btnImport = document.getElementById('btnImport');
  const importStatus = document.getElementById('importStatus');

  btnImport.addEventListener('click', function() {
    const perfPeriodId = perfPeriodSelect.value;
    if (!perfPeriodId) {
      importStatus.innerHTML = '<span class="text-warning">Pilih periode performansi dulu.</span>';
      return;
    }

    btnImport.disabled = true;
    importStatus.innerHTML = '<span class="text-info"><i class="fas fa-spinner fa-spin"></i> Mengambil data...</span>';

    fetch('<?= base_url('admin/awarding/scores/get-performance-data') ?>?website_id=' + websiteId + '&perf_period_id=' + perfPeriodId)
      .then(r => r.json())
      .then(function(res) {
        if (res.success) {
          document.getElementById('clicks_web').value = res.data.clicks_web;
          document.getElementById('clicks_mobile').value = res.data.clicks_mobile;
          document.getElementById('clicks_tablet').value = res.data.clicks_tablet;
          document.getElementById('total_posts').value = res.data.total_posts;
          document.getElementById('is_imported').value = '1';
          updateTotalClicks();
          importStatus.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Data berhasil diimport!</span>';
        } else {
          importStatus.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i> ' + res.message + '</span>';
        }
        btnImport.disabled = false;
      })
      .catch(function() {
        importStatus.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i> Gagal mengambil data.</span>';
        btnImport.disabled = false;
      });
  });
});
</script>
<?= $this->endSection() ?>
