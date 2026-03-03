<?php
// Prepare JSON data for JavaScript charts
$trendLabelsJson    = json_encode($trendLabels);
$trendSeriesJson    = json_encode($trendSeries);
$postsBarLabelsJson = json_encode($postsBarLabels);
$postsBarDataJson   = json_encode($postsBarData);
$deviceData         = json_encode([$totalWeb, $totalMobile, $totalTablet]);
?>

<!-- Filter Bar -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body py-3">
        <form method="get" class="form-inline">
          <div class="form-group mr-3">
            <label for="period_id" class="mr-2"><strong>Periode:</strong></label>
            <select class="form-control" id="period_id" name="period_id" onchange="this.form.submit()">
              <?php foreach ($periods as $period): ?>
              <option value="<?= $period['id'] ?>" <?= $selectedPeriodId == $period['id'] ? 'selected' : '' ?>>
                <?= esc($period['period_name']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group mr-3">
            <label for="website_id" class="mr-2"><strong>Website:</strong></label>
            <select class="form-control" id="website_id" name="website_id" onchange="this.form.submit()">
              <option value="">Semua Website</option>
              <?php foreach ($websites as $website): ?>
              <option value="<?= $website['id'] ?>" <?= $selectedWebsiteId == $website['id'] ? 'selected' : '' ?>>
                <?= esc($website['website_name']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Widget Ringkasan -->
<div class="row">
  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card card-statistic-1">
      <div class="card-icon bg-primary">
        <i class="fas fa-mouse-pointer"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header"><h4>Total Klik <?= $selectedWebsiteId ? 'Website' : 'Global' ?></h4></div>
        <div class="card-body"><?= number_format($totalClicksAll) ?></div>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card card-statistic-1">
      <div class="card-icon bg-success">
        <i class="fas fa-file-alt"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header"><h4>Total Post Baru</h4></div>
        <div class="card-body"><?= number_format($totalPosts) ?></div>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card card-statistic-1">
      <div class="card-icon bg-warning">
        <i class="fas fa-globe"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header"><h4>Total Website</h4></div>
        <div class="card-body"><?= $totalWebsites ?></div>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card card-statistic-1">
      <div class="card-icon bg-info">
        <i class="fas fa-chart-line"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header"><h4>Data Terinput</h4></div>
        <div class="card-body"><?= count($summaryData) ?></div>
      </div>
    </div>
  </div>
</div>

<!-- Grafik Baris 1: Tren Klik & Pie Chart Perangkat -->
<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-chart-line"></i> Tren Klik Antar Periode</h4>
      </div>
      <div class="card-body">
        <?php if (empty($trendLabels)): ?>
        <div class="text-center text-muted py-5">
          <i class="fas fa-chart-line fa-3x mb-3"></i>
          <p>Belum ada data untuk ditampilkan.</p>
        </div>
        <?php else: ?>
        <canvas id="trendChart" height="120"></canvas>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-chart-pie"></i> Segmentasi Perangkat</h4>
      </div>
      <div class="card-body">
        <?php if ($totalClicksAll === 0): ?>
        <div class="text-center text-muted py-5">
          <i class="fas fa-chart-pie fa-3x mb-3"></i>
          <p>Belum ada data klik.</p>
        </div>
        <?php else: ?>
        <canvas id="deviceChart" height="200"></canvas>
        <div class="mt-3">
          <div class="d-flex justify-content-between mb-1">
            <span><i class="fas fa-circle text-primary"></i> Desktop</span>
            <strong><?= number_format($totalWeb) ?></strong>
          </div>
          <div class="d-flex justify-content-between mb-1">
            <span><i class="fas fa-circle text-success"></i> Mobile</span>
            <strong><?= number_format($totalMobile) ?></strong>
          </div>
          <div class="d-flex justify-content-between">
            <span><i class="fas fa-circle text-warning"></i> Tablet</span>
            <strong><?= number_format($totalTablet) ?></strong>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Grafik Baris 2: Bar Chart Postingan -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-chart-bar"></i> Perbandingan Jumlah Postingan Antar Website</h4>
      </div>
      <div class="card-body">
        <?php if (empty($postsBarLabels)): ?>
        <div class="text-center text-muted py-5">
          <i class="fas fa-chart-bar fa-3x mb-3"></i>
          <p>Belum ada data untuk ditampilkan.</p>
        </div>
        <?php else: ?>
        <canvas id="postsBarChart" height="80"></canvas>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Baris 3: Timeline Update & Leaderboard -->
<div class="row">
  <!-- Timeline Update Terakhir -->
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-clock"></i> Timeline Update Terakhir</h4>
      </div>
      <div class="card-body">
        <?php if (empty($timelineData)): ?>
        <div class="text-center text-muted py-4">Belum ada data.</div>
        <?php else: ?>
        <div class="table-responsive">
          <table class="table table-sm table-hover">
            <thead>
              <tr>
                <th>Nama Website</th>
                <th>Update Terakhir</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($timelineData as $row): ?>
              <tr>
                <td><?= esc($row['website_name']) ?></td>
                <td>
                  <?php if (!empty($row['last_post_date'])): ?>
                    <?= date('d M Y', strtotime($row['last_post_date'])) ?>
                  <?php else: ?>
                    <span class="text-muted">Belum ada</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php
                    if (empty($row['last_post_date'])) {
                        echo '<span class="badge badge-secondary">Belum Ada Data</span>';
                    } else {
                        $daysDiff = (int) ((time() - strtotime($row['last_post_date'])) / 86400);
                        if ($daysDiff <= 7) {
                            echo '<span class="badge badge-success">Aktif</span>';
                        } elseif ($daysDiff <= 30) {
                            echo '<span class="badge badge-warning">Agak Lama</span>';
                        } else {
                            echo '<span class="badge badge-danger">Jarang Update (' . $daysDiff . ' hari)</span>';
                        }
                    }
                  ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Leaderboard Artikel -->
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-trophy"></i> Leaderboard Artikel Terpopuler</h4>
      </div>
      <div class="card-body">
        <?php if (empty($leaderboard)): ?>
        <div class="text-center text-muted py-4">Belum ada data artikel.</div>
        <?php else: ?>
        <div class="table-responsive">
          <table class="table table-sm table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>Judul Artikel</th>
                <th>Website</th>
                <th>Klik</th>
              </tr>
            </thead>
            <tbody>
              <?php $rank = 1; foreach (array_slice($leaderboard, 0, 10) as $article): ?>
              <tr>
                <td>
                  <?php if ($rank <= 3): ?>
                    <span class="badge badge-<?= $rank === 1 ? 'warning' : ($rank === 2 ? 'secondary' : 'info') ?>">
                      <i class="fas fa-medal"></i> <?= $rank ?>
                    </span>
                  <?php else: ?>
                    <?= $rank ?>
                  <?php endif; ?>
                </td>
                <td>
                  <span title="<?= esc($article['article_title']) ?>">
                    <?= esc(mb_strlen($article['article_title']) > 50 ? mb_substr($article['article_title'], 0, 50) . '...' : $article['article_title']) ?>
                  </span>
                </td>
                <td><small><?= esc($article['website_name']) ?></small></td>
                <td><strong><?= number_format($article['article_clicks']) ?></strong></td>
              </tr>
              <?php $rank++; endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Tabel Ringkasan Performansi -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-table"></i> Ringkasan Performansi Per Website</h4>
      </div>
      <div class="card-body">
        <?php if (empty($summaryData)): ?>
        <div class="text-center text-muted py-4">Belum ada data untuk periode ini.</div>
        <?php else: ?>
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Nama Website</th>
                <th>Desktop</th>
                <th>Mobile</th>
                <th>Tablet</th>
                <th>Total Klik</th>
                <th>Post Baru</th>
                <th>Update Terakhir</th>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1; foreach ($summaryData as $row): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><strong><?= esc($row['website_name']) ?></strong></td>
                <td><?= number_format($row['clicks_web']) ?></td>
                <td><?= number_format($row['clicks_mobile']) ?></td>
                <td><?= number_format($row['clicks_tablet']) ?></td>
                <td><strong class="text-primary"><?= number_format($row['total_clicks']) ?></strong></td>
                <td><?= number_format($row['total_new_posts']) ?></td>
                <td>
                  <?= !empty($row['last_post_date']) ? date('d M Y', strtotime($row['last_post_date'])) : '<span class="text-muted">-</span>' ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?= $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Color palette
  const colors = [
    '#6777ef', '#63ed7a', '#ffa426', '#fc544b', '#3abaf4',
    '#e83e8c', '#6f42c1', '#fd7e14', '#20c997', '#17a2b8',
    '#6610f2', '#007bff', '#28a745', '#dc3545', '#ffc107'
  ];

  // 1. Tren Chart (Line)
  <?php if (!empty($trendLabels)): ?>
  const trendLabels = <?= $trendLabelsJson ?>;
  const trendSeries = <?= $trendSeriesJson ?>;
  const trendDatasets = [];
  let colorIdx = 0;

  for (const [website, data] of Object.entries(trendSeries)) {
    const color = colors[colorIdx % colors.length];
    const dataPoints = trendLabels.map(label => {
      const found = data.find(d => d.period === label);
      return found ? found.total_clicks : 0;
    });
    trendDatasets.push({
      label: website,
      data: dataPoints,
      borderColor: color,
      backgroundColor: color + '20',
      tension: 0.3,
      fill: true,
    });
    colorIdx++;
  }

  new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: { labels: trendLabels, datasets: trendDatasets },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' }
      },
      scales: {
        y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString() } }
      }
    }
  });
  <?php endif; ?>

  // 2. Device Pie Chart
  <?php if ($totalClicksAll > 0): ?>
  new Chart(document.getElementById('deviceChart'), {
    type: 'doughnut',
    data: {
      labels: ['Desktop', 'Mobile', 'Tablet'],
      datasets: [{
        data: <?= $deviceData ?>,
        backgroundColor: ['#6777ef', '#63ed7a', '#ffa426'],
        borderWidth: 2,
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' }
      }
    }
  });
  <?php endif; ?>

  // 3. Posts Bar Chart
  <?php if (!empty($postsBarLabels)): ?>
  new Chart(document.getElementById('postsBarChart'), {
    type: 'bar',
    data: {
      labels: <?= $postsBarLabelsJson ?>,
      datasets: [{
        label: 'Jumlah Post Baru',
        data: <?= $postsBarDataJson ?>,
        backgroundColor: colors.slice(0, <?= count($postsBarLabels) ?>),
        borderWidth: 1,
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: { beginAtZero: true, ticks: { stepSize: 1 } }
      }
    }
  });
  <?php endif; ?>
});
</script>
<?= $this->endSection() ?>
