<div class="row">
  <div class="col-12 col-md-8 offset-md-2">
    <div class="card">
      <div class="card-header">
        <h4>Edit Website</h4>
      </div>
      <div class="card-body">
        <form action="<?= base_url('admin/websites/update/' . $website['id']) ?>" method="post">
          <?= csrf_field() ?>

          <div class="form-row">
            <div class="form-group col-md-8">
              <label for="website_name">Nama Website <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="website_name" name="website_name" 
                     value="<?= old('website_name', $website['website_name']) ?>" required>
            </div>
            <div class="form-group col-md-4">
              <label for="category">Kategori <span class="text-danger">*</span></label>
              <select class="form-control" id="category" name="category" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($categories as $key => $label): ?>
                <option value="<?= $key ?>" <?= old('category', $website['category']) === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="url">URL Website <span class="text-danger">*</span></label>
            <input type="url" class="form-control" id="url" name="url" 
                   value="<?= old('url', $website['url']) ?>" required>
          </div>

          <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?= old('description', $website['description']) ?></textarea>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="admin_name">Nama Admin Website</label>
              <input type="text" class="form-control" id="admin_name" name="admin_name" 
                     value="<?= old('admin_name', $website['admin_name']) ?>">
            </div>
            <div class="form-group col-md-6">
              <label for="admin_contact">Kontak Admin</label>
              <input type="text" class="form-control" id="admin_contact" name="admin_contact" 
                     value="<?= old('admin_contact', $website['admin_contact']) ?>">
            </div>
          </div>

          <div class="form-group">
            <label for="status">Status <span class="text-danger">*</span></label>
            <select class="form-control" id="status" name="status" required>
              <option value="active" <?= old('status', $website['status']) === 'active' ? 'selected' : '' ?>>Aktif</option>
              <option value="inactive" <?= old('status', $website['status']) === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
            </select>
          </div>

          <div class="form-group text-right">
            <a href="<?= base_url('admin/websites') ?>" class="btn btn-secondary mr-1">Batal</a>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Simpan Perubahan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
