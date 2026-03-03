<div class="row">
  <div class="col-12 col-md-8 offset-md-2">
    <div class="card">
      <div class="card-header">
        <h4>Tambah Periode Awarding</h4>
      </div>
      <div class="card-body">
        <form action="<?= base_url('admin/awarding/periods/store') ?>" method="post">
          <?= csrf_field() ?>

          <div class="form-group">
            <label for="period_name">Nama Periode Awarding <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="period_name" name="period_name"
                   value="<?= old('period_name') ?>" placeholder="Contoh: Awarding Semester Genap 2025/2026" required>
          </div>

          <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea class="form-control" id="description" name="description" rows="3"
                      placeholder="Deskripsi singkat tentang periode awarding ini"><?= old('description') ?></textarea>
          </div>

          <div class="form-group">
            <label for="performance_period_id">Periode Performansi (Opsional)</label>
            <select class="form-control" id="performance_period_id" name="performance_period_id">
              <option value="">-- Tidak terkait --</option>
              <?php foreach ($perfPeriods as $pp): ?>
              <option value="<?= $pp['id'] ?>" <?= old('performance_period_id') == $pp['id'] ? 'selected' : '' ?>>
                <?= esc($pp['period_name']) ?> <?= $pp['status'] === 'closed' ? '(Closed)' : '(Open)' ?>
              </option>
              <?php endforeach; ?>
            </select>
            <small class="form-text text-muted">Pilih periode performansi jika ingin mengimpor data analytics & konten dari modul Performansi Website.</small>
          </div>

          <div class="form-group text-right">
            <a href="<?= base_url('admin/awarding/periods') ?>" class="btn btn-secondary mr-1">Batal</a>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
