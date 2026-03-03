<div class="row">
  <div class="col-12 col-md-8 offset-md-2">
    <div class="card">
      <div class="card-header">
        <h4>Tambah Periode Baru</h4>
      </div>
      <div class="card-body">
        <form action="<?= base_url('admin/periods/store') ?>" method="post">
          <?= csrf_field() ?>

          <div class="form-group">
            <label for="period_name">Nama Periode <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="period_name" name="period_name" 
                   value="<?= old('period_name') ?>" placeholder="Contoh: Maret 2026" required>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="start_date">Tanggal Mulai</label>
              <input type="date" class="form-control" id="start_date" name="start_date" value="<?= old('start_date') ?>">
            </div>
            <div class="form-group col-md-6">
              <label for="end_date">Tanggal Selesai</label>
              <input type="date" class="form-control" id="end_date" name="end_date" value="<?= old('end_date') ?>">
            </div>
          </div>

          <div class="form-group text-right">
            <a href="<?= base_url('admin/periods') ?>" class="btn btn-secondary mr-1">Batal</a>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
