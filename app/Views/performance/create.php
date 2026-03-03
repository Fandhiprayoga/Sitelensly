<div class="row">
  <div class="col-12 col-md-10 offset-md-1">
    <div class="card">
      <div class="card-header">
        <h4>Input Data Performansi</h4>
      </div>
      <div class="card-body">
        <?php if (empty($websites)): ?>
        <div class="alert alert-warning">
          <i class="fas fa-exclamation-triangle"></i> Belum ada website yang terdaftar.
          <a href="<?= base_url('admin/websites/create') ?>">Tambah website terlebih dahulu.</a>
        </div>
        <?php elseif (empty($periods)): ?>
        <div class="alert alert-warning">
          <i class="fas fa-exclamation-triangle"></i> Belum ada periode yang berstatus Open.
          <a href="<?= base_url('admin/periods/create') ?>">Buat periode terlebih dahulu.</a>
        </div>
        <?php else: ?>
        <form action="<?= base_url('admin/performance/store') ?>" method="post">
          <?= csrf_field() ?>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="website_id">Website <span class="text-danger">*</span></label>
              <select class="form-control" id="website_id" name="website_id" required>
                <option value="">-- Pilih Website --</option>
                <?php foreach ($websites as $website): ?>
                <option value="<?= $website['id'] ?>" <?= old('website_id') == $website['id'] ? 'selected' : '' ?>>
                  <?= esc($website['website_name']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group col-md-6">
              <label for="period_id">Periode <span class="text-danger">*</span></label>
              <select class="form-control" id="period_id" name="period_id" required>
                <option value="">-- Pilih Periode --</option>
                <?php foreach ($periods as $period): ?>
                <option value="<?= $period['id'] ?>" <?= old('period_id') == $period['id'] ? 'selected' : '' ?>>
                  <?= esc($period['period_name']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <hr>
          <h6 class="text-primary mb-3"><i class="fas fa-mouse-pointer"></i> Data Klik per Perangkat</h6>
          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="clicks_web">Klik Desktop <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="clicks_web" name="clicks_web" 
                     value="<?= old('clicks_web', 0) ?>" min="0" required>
            </div>
            <div class="form-group col-md-4">
              <label for="clicks_mobile">Klik Mobile <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="clicks_mobile" name="clicks_mobile" 
                     value="<?= old('clicks_mobile', 0) ?>" min="0" required>
            </div>
            <div class="form-group col-md-4">
              <label for="clicks_tablet">Klik Tablet <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="clicks_tablet" name="clicks_tablet" 
                     value="<?= old('clicks_tablet', 0) ?>" min="0" required>
            </div>
          </div>

          <hr>
          <h6 class="text-primary mb-3"><i class="fas fa-file-alt"></i> Data Postingan</h6>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="total_new_posts">Jumlah Post Baru <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="total_new_posts" name="total_new_posts" 
                     value="<?= old('total_new_posts', 0) ?>" min="0" required>
            </div>
            <div class="form-group col-md-6">
              <label for="last_post_date">Tanggal Update Terakhir</label>
              <input type="date" class="form-control" id="last_post_date" name="last_post_date" 
                     value="<?= old('last_post_date') ?>">
            </div>
          </div>

          <hr>
          <h6 class="text-primary mb-3"><i class="fas fa-trophy"></i> 3 Artikel Teratas</h6>
          <?php for ($i = 0; $i < 3; $i++): ?>
          <div class="form-row">
            <div class="form-group col-md-1">
              <label>&nbsp;</label>
              <div class="form-control-plaintext text-center">
                <span class="badge badge-primary badge-lg">#<?= $i + 1 ?></span>
              </div>
            </div>
            <div class="form-group col-md-8">
              <label for="article_title_<?= $i ?>">Judul Artikel <?= $i + 1 ?></label>
              <input type="text" class="form-control" id="article_title_<?= $i ?>" 
                     name="articles[<?= $i ?>][title]" value="<?= old("articles.{$i}.title") ?>"
                     placeholder="Judul artikel teratas ke-<?= $i + 1 ?>">
            </div>
            <div class="form-group col-md-3">
              <label for="article_clicks_<?= $i ?>">Jumlah Klik</label>
              <input type="number" class="form-control" id="article_clicks_<?= $i ?>" 
                     name="articles[<?= $i ?>][clicks]" value="<?= old("articles.{$i}.clicks", 0) ?>" min="0">
            </div>
          </div>
          <?php endfor; ?>

          <div class="form-group text-right">
            <a href="<?= base_url('admin/performance') ?>" class="btn btn-secondary mr-1">Batal</a>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Simpan Data
            </button>
          </div>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
