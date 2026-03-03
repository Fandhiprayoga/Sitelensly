<div class="row">
  <div class="col-12 col-md-8 offset-md-2">
    <div class="card">
      <div class="card-header">
        <h4>Edit Periode</h4>
      </div>
      <div class="card-body">
        <form action="<?= base_url('admin/periods/update/' . $period['id']) ?>" method="post">
          <?= csrf_field() ?>

          <div class="form-group">
            <label for="period_name">Nama Periode <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="period_name" name="period_name" 
                   value="<?= old('period_name', $period['period_name']) ?>" required>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="start_date">Tanggal Mulai</label>
              <input type="date" class="form-control" id="start_date" name="start_date" 
                     value="<?= old('start_date', $period['start_date']) ?>">
            </div>
            <div class="form-group col-md-6">
              <label for="end_date">Tanggal Selesai</label>
              <input type="date" class="form-control" id="end_date" name="end_date" 
                     value="<?= old('end_date', $period['end_date']) ?>">
            </div>
          </div>

          <div class="form-group">
            <label for="status">Status <span class="text-danger">*</span></label>
            <select class="form-control" id="status" name="status" required>
              <option value="open" <?= old('status', $period['status']) === 'open' ? 'selected' : '' ?>>Open</option>
              <option value="closed" <?= old('status', $period['status']) === 'closed' ? 'selected' : '' ?>>Closed</option>
            </select>
          </div>

          <div class="form-group text-right">
            <a href="<?= base_url('admin/periods') ?>" class="btn btn-secondary mr-1">Batal</a>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Simpan Perubahan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
