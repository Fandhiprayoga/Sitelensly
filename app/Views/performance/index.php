<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>Data Performansi Website</h4>
        <div class="card-header-action">
          <?php if (activeGroupCan('performance.input')): ?>
          <a href="<?= base_url('admin/performance/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Input Data
          </a>
          <?php endif; ?>
        </div>
      </div>
      <div class="card-body">
        <!-- Filter Periode -->
        <form method="get" class="mb-4">
          <div class="form-row align-items-end">
            <div class="form-group col-md-4 mb-0">
              <label for="period_id">Filter Periode</label>
              <select class="form-control" id="period_id" name="period_id" onchange="this.form.submit()">
                <?php foreach ($periods as $period): ?>
                <option value="<?= $period['id'] ?>" <?= $selectedPeriodId == $period['id'] ? 'selected' : '' ?>>
                  <?= esc($period['period_name']) ?> <?= $period['status'] === 'closed' ? '(Closed)' : '' ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </form>

        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>Nama Website</th>
                <th>Klik Desktop</th>
                <th>Klik Mobile</th>
                <th>Klik Tablet</th>
                <th>Total Klik</th>
                <th>Post Baru</th>
                <th>Update Terakhir</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($performances)): ?>
              <tr>
                <td colspan="9" class="text-center text-muted">Belum ada data untuk periode ini.</td>
              </tr>
              <?php else: ?>
              <?php $no = 1; foreach ($performances as $perf): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td>
                  <strong><?= esc($perf['website_name']) ?></strong>
                  <?php if (!empty($perf['website_category'])): ?>
                    <span class="badge badge-light"><?= esc(ucfirst($perf['website_category'])) ?></span>
                  <?php endif; ?>
                  <br><small class="text-muted"><?= esc($perf['url']) ?></small>
                </td>
                <td><?= number_format($perf['clicks_web']) ?></td>
                <td><?= number_format($perf['clicks_mobile']) ?></td>
                <td><?= number_format($perf['clicks_tablet']) ?></td>
                <td><strong><?= number_format($perf['total_clicks']) ?></strong></td>
                <td><?= number_format($perf['total_new_posts']) ?></td>
                <td>
                  <?= $perf['last_post_date'] ? date('d M Y', strtotime($perf['last_post_date'])) : '<span class="text-muted">-</span>' ?>
                </td>
                <td>
                  <!-- Detail Artikel -->
                  <?php if (!empty($perf['articles'])): ?>
                  <button type="button" class="btn btn-sm btn-info btn-articles-modal" data-id="<?= $perf['id'] ?>" title="Lihat Artikel">
                    <i class="fas fa-newspaper"></i>
                  </button>
                  <?php endif; ?>
                  <?php if (activeGroupCan('performance.edit') && ($perf['period_status'] ?? '') === 'open'): ?>
                  <a href="<?= base_url('admin/performance/edit/' . $perf['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                  <?php endif; ?>
                  <?php if (activeGroupCan('performance.delete')): ?>
                  <form action="<?= base_url('admin/performance/delete/' . $perf['id']) ?>" method="post" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">
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

<!-- Modals untuk detail artikel -->
<?php if (!empty($performances)): ?>
<?php foreach ($performances as $perf): ?>
<?php if (!empty($perf['articles'])): ?>
<div class="modal fade" id="articlesModal<?= $perf['id'] ?>" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Artikel Teratas - <?= esc($perf['website_name']) ?></h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <table class="table table-sm">
          <thead>
            <tr>
              <th>Rank</th>
              <th>Judul Artikel</th>
              <th>Jumlah Klik</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($perf['articles'] as $article): ?>
            <tr>
              <td><span class="badge badge-primary">#<?= $article['rank'] ?></span></td>
              <td><?= esc($article['article_title']) ?></td>
              <td><?= number_format($article['article_clicks']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Move modals to body to avoid stacking context / overflow issues
    document.querySelectorAll('.modal').forEach(function(modal) {
        document.body.appendChild(modal);
    });

    // Explicit modal trigger (bypass Stisla data-api interference)
    $(document).on('click', '.btn-articles-modal', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        $('#articlesModal' + id).modal('show');
    });
});
</script>
