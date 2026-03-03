<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>Laporan Ringkas Performansi</h4>
        <div class="card-header-action">
          <?php if (activeGroupCan('reports.export') && !empty($performances)): ?>
          <a href="<?= base_url('admin/reports/export-csv?period_id=' . $selectedPeriodId) ?>" class="btn btn-success">
            <i class="fas fa-file-csv"></i> Export CSV
          </a>
          <?php endif; ?>
        </div>
      </div>
      <div class="card-body">
        <!-- Filter Periode -->
        <form method="get" class="mb-4">
          <div class="form-row align-items-end">
            <div class="form-group col-md-4 mb-0">
              <label for="period_id">Pilih Periode</label>
              <select class="form-control" id="period_id" name="period_id" onchange="this.form.submit()">
                <?php foreach ($periods as $period): ?>
                <option value="<?= $period['id'] ?>" <?= $selectedPeriodId == $period['id'] ? 'selected' : '' ?>>
                  <?= esc($period['period_name']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </form>

        <?php if (empty($performances)): ?>
        <div class="text-center text-muted py-5">
          <i class="fas fa-file-alt fa-3x mb-3"></i>
          <p>Belum ada data untuk periode ini.</p>
        </div>
        <?php else: ?>

        <!-- Ringkasan Cepat -->
        <?php
          $totalAllClicks = array_sum(array_column($performances, 'total_clicks'));
          $totalAllPosts  = array_sum(array_column($performances, 'total_new_posts'));
          $topWebsite     = $performances[0] ?? null;
        ?>
        <div class="row mb-4">
          <div class="col-md-4">
            <div class="card bg-primary text-white">
              <div class="card-body text-center">
                <h5>Total Klik Semua Website</h5>
                <h2><?= number_format($totalAllClicks) ?></h2>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card bg-success text-white">
              <div class="card-body text-center">
                <h5>Total Post Baru</h5>
                <h2><?= number_format($totalAllPosts) ?></h2>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card bg-warning text-white">
              <div class="card-body text-center">
                <h5>Website Terbanyak Klik</h5>
                <h2><?= $topWebsite ? esc($topWebsite['website_name']) : '-' ?></h2>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabel Rekapitulasi -->
        <div class="table-responsive" id="reportTable">
          <table class="table table-striped table-bordered">
            <thead class="thead-dark">
              <tr>
                <th rowspan="2" class="align-middle text-center">Peringkat</th>
                <th rowspan="2" class="align-middle">Nama Website</th>
                <th colspan="3" class="text-center">Klik per Perangkat</th>
                <th rowspan="2" class="align-middle text-center">Total Klik</th>
                <th rowspan="2" class="align-middle text-center">Post Baru</th>
                <th rowspan="2" class="align-middle text-center">Update Terakhir</th>
                <th colspan="3" class="text-center">Artikel Teratas</th>
              </tr>
              <tr>
                <th class="text-center">Desktop</th>
                <th class="text-center">Mobile</th>
                <th class="text-center">Tablet</th>
                <th>Judul</th>
                <th class="text-center">Klik</th>
                <th class="text-center">Rank</th>
              </tr>
            </thead>
            <tbody>
              <?php $rank = 1; foreach ($performances as $perf): ?>
              <?php $articleCount = max(count($perf['articles']), 1); ?>
              <tr>
                <td rowspan="<?= $articleCount ?>" class="align-middle text-center">
                  <?php if ($rank <= 3): ?>
                    <span class="badge badge-lg badge-<?= $rank === 1 ? 'warning' : ($rank === 2 ? 'secondary' : 'info') ?>">
                      <i class="fas fa-medal"></i> <?= $rank ?>
                    </span>
                  <?php else: ?>
                    <?= $rank ?>
                  <?php endif; ?>
                </td>
                <td rowspan="<?= $articleCount ?>" class="align-middle">
                  <strong><?= esc($perf['website_name']) ?></strong>
                  <?php if (!empty($perf['website_category'])): ?>
                    <br><span class="badge badge-light"><?= esc(ucfirst($perf['website_category'])) ?></span>
                  <?php endif; ?>
                  <br><small class="text-muted"><?= esc($perf['url'] ?? '') ?></small>
                </td>
                <td rowspan="<?= $articleCount ?>" class="align-middle text-center"><?= number_format($perf['clicks_web']) ?></td>
                <td rowspan="<?= $articleCount ?>" class="align-middle text-center"><?= number_format($perf['clicks_mobile']) ?></td>
                <td rowspan="<?= $articleCount ?>" class="align-middle text-center"><?= number_format($perf['clicks_tablet']) ?></td>
                <td rowspan="<?= $articleCount ?>" class="align-middle text-center"><strong class="text-primary"><?= number_format($perf['total_clicks']) ?></strong></td>
                <td rowspan="<?= $articleCount ?>" class="align-middle text-center"><?= number_format($perf['total_new_posts']) ?></td>
                <td rowspan="<?= $articleCount ?>" class="align-middle text-center">
                  <?= !empty($perf['last_post_date']) ? date('d M Y', strtotime($perf['last_post_date'])) : '-' ?>
                </td>
                <?php if (!empty($perf['articles'])): ?>
                <td><?= esc($perf['articles'][0]['article_title']) ?></td>
                <td class="text-center"><?= number_format($perf['articles'][0]['article_clicks']) ?></td>
                <td class="text-center">#<?= $perf['articles'][0]['rank'] ?></td>
                <?php else: ?>
                <td colspan="3" class="text-muted text-center">-</td>
                <?php endif; ?>
              </tr>
              <?php if (count($perf['articles']) > 1): ?>
                <?php for ($i = 1; $i < count($perf['articles']); $i++): ?>
                <tr>
                  <td><?= esc($perf['articles'][$i]['article_title']) ?></td>
                  <td class="text-center"><?= number_format($perf['articles'][$i]['article_clicks']) ?></td>
                  <td class="text-center">#<?= $perf['articles'][$i]['rank'] ?></td>
                </tr>
                <?php endfor; ?>
              <?php endif; ?>
              <?php $rank++; endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?= $this->section('page_js') ?>
<script>
// Print functionality
function printReport() {
  var content = document.getElementById('reportTable').innerHTML;
  var win = window.open('', '_blank');
  win.document.write('<html><head><title>Laporan Performansi</title>');
  win.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">');
  win.document.write('</head><body class="p-4">');
  win.document.write('<h3 class="mb-4">Laporan Ringkas Performansi Website</h3>');
  win.document.write(content);
  win.document.write('</body></html>');
  win.document.close();
  win.setTimeout(function(){ win.print(); }, 500);
}
</script>
<?= $this->endSection() ?>
