<?php
// Siapkan data artikel yang sudah ada
$existingArticles = [];
if (!empty($articles)) {
    foreach ($articles as $article) {
        $existingArticles[$article['rank'] - 1] = $article;
    }
}
?>
<div class="row">
  <div class="col-12 col-md-10 offset-md-1">
    <div class="card">
      <div class="card-header">
        <h4>Edit Data Performansi</h4>
      </div>
      <div class="card-body">
        <form action="<?= base_url('admin/performance/update/' . $performance['id']) ?>" method="post">
          <?= csrf_field() ?>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Website</label>
              <?php foreach ($websites as $website): ?>
                <?php if ($website['id'] == $performance['website_id']): ?>
                <input type="text" class="form-control" value="<?= esc($website['website_name']) ?>" disabled>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
            <div class="form-group col-md-6">
              <label>Periode</label>
              <?php foreach ($periods as $period): ?>
                <?php if ($period['id'] == $performance['period_id']): ?>
                <input type="text" class="form-control" value="<?= esc($period['period_name']) ?>" disabled>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          </div>

          <hr>
          <h6 class="text-primary mb-3"><i class="fas fa-mouse-pointer"></i> Data Klik per Perangkat</h6>
          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="clicks_web">Klik Desktop <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="clicks_web" name="clicks_web" 
                     value="<?= old('clicks_web', $performance['clicks_web']) ?>" min="0" required>
            </div>
            <div class="form-group col-md-4">
              <label for="clicks_mobile">Klik Mobile <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="clicks_mobile" name="clicks_mobile" 
                     value="<?= old('clicks_mobile', $performance['clicks_mobile']) ?>" min="0" required>
            </div>
            <div class="form-group col-md-4">
              <label for="clicks_tablet">Klik Tablet <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="clicks_tablet" name="clicks_tablet" 
                     value="<?= old('clicks_tablet', $performance['clicks_tablet']) ?>" min="0" required>
            </div>
          </div>

          <hr>
          <h6 class="text-primary mb-3"><i class="fas fa-file-alt"></i> Data Postingan</h6>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="total_new_posts">Jumlah Post Baru <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="total_new_posts" name="total_new_posts" 
                     value="<?= old('total_new_posts', $performance['total_new_posts']) ?>" min="0" required>
            </div>
            <div class="form-group col-md-6">
              <label for="last_post_date">Tanggal Update Terakhir</label>
              <input type="date" class="form-control" id="last_post_date" name="last_post_date" 
                     value="<?= old('last_post_date', $performance['last_post_date']) ?>">
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
                     name="articles[<?= $i ?>][title]" 
                     value="<?= old("articles.{$i}.title", $existingArticles[$i]['article_title'] ?? '') ?>">
            </div>
            <div class="form-group col-md-3">
              <label for="article_clicks_<?= $i ?>">Jumlah Klik</label>
              <input type="number" class="form-control" id="article_clicks_<?= $i ?>" 
                     name="articles[<?= $i ?>][clicks]" 
                     value="<?= old("articles.{$i}.clicks", $existingArticles[$i]['article_clicks'] ?? 0) ?>" min="0">
            </div>
          </div>
          <?php endfor; ?>

          <div class="form-group text-right">
            <a href="<?= base_url('admin/performance') ?>" class="btn btn-secondary mr-1">Batal</a>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Simpan Perubahan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
