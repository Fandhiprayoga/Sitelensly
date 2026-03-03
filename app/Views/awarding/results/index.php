<?php
// Prepare badge colors for top 3
function rankBadge(int $rank): string {
    return match($rank) {
        1 => 'badge-warning',
        2 => 'badge-secondary',
        3 => 'badge-info',
        default => 'badge-light',
    };
}
?>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>Hasil & Peringkat Awarding (SAW)</h4>
        <div class="card-header-action">
          <?php if (!empty($results) && activeGroupCan('awarding.results.export')): ?>
          <a href="<?= base_url('admin/awarding/results/export-csv?period_id=' . $selectedPeriodId) ?>" class="btn btn-success">
            <i class="fas fa-file-csv"></i> Export CSV
          </a>
          <?php endif; ?>
        </div>
      </div>
      <div class="card-body">
        <!-- Pilih Periode -->
        <form method="get" class="mb-4">
          <div class="form-row align-items-end">
            <div class="form-group col-md-4 mb-0">
              <label for="period_id">Periode Awarding</label>
              <select class="form-control" id="period_id" name="period_id" onchange="this.form.submit()">
                <?php if (empty($periods)): ?>
                <option value="">-- Belum ada periode --</option>
                <?php endif; ?>
                <?php foreach ($periods as $period): ?>
                <option value="<?= $period['id'] ?>" <?= $selectedPeriodId == $period['id'] ? 'selected' : '' ?>>
                  <?= esc($period['period_name']) ?>
                  (<?= match($period['status']) { 'draft' => 'Draft', 'active' => 'Aktif', 'completed' => 'Selesai', default => $period['status'] } ?>)
                </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </form>

        <?php if (!$isWeightReady): ?>
        <div class="alert alert-warning">
          <i class="fas fa-exclamation-triangle"></i> Bobot penilaian belum lengkap untuk periode ini.
          <?php if (activeGroupCan('awarding.weights.manage')): ?>
          <a href="<?= base_url('admin/awarding/weights?period_id=' . $selectedPeriodId) ?>">Set bobot sekarang →</a>
          <?php endif; ?>
        </div>
        <?php elseif (empty($results)): ?>
        <div class="text-center text-muted py-5">
          <i class="fas fa-trophy fa-3x mb-3"></i>
          <p>Belum ada data penilaian untuk periode ini.</p>
        </div>
        <?php else: ?>

        <!-- Bobot yang digunakan -->
        <div class="row mb-4">
          <?php foreach ($weights as $code => $w): ?>
          <div class="col-md-4">
            <div class="card bg-light border">
              <div class="card-body py-2 text-center">
                <small class="text-muted"><?= esc($w['criteria_name']) ?></small>
                <h4 class="mb-0 text-primary"><?= number_format($w['weight_value'], 4) ?></h4>
                <span class="badge badge-<?= $w['criteria_type'] === 'benefit' ? 'success' : 'danger' ?>"><?= ucfirst($w['criteria_type']) ?></span>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Top 3 Winners -->
        <?php if (count($results) >= 1): ?>
        <div class="row mb-4">
          <?php foreach (array_slice($results, 0, 3) as $winner): ?>
          <div class="col-md-4">
            <div class="card <?= $winner['rank'] === 1 ? 'border-warning' : '' ?>">
              <div class="card-body text-center">
                <div class="mb-2">
                  <?php if ($winner['rank'] === 1): ?>
                    <i class="fas fa-trophy fa-3x text-warning"></i>
                  <?php elseif ($winner['rank'] === 2): ?>
                    <i class="fas fa-medal fa-2x text-secondary"></i>
                  <?php else: ?>
                    <i class="fas fa-award fa-2x text-info"></i>
                  <?php endif; ?>
                </div>
                <h5 class="mb-1">#<?= $winner['rank'] ?></h5>
                <h6 class="mb-1"><?= esc($winner['website_name']) ?></h6>
                <small class="text-muted"><?= ucfirst($winner['website_category'] ?? '') ?></small>
                <hr class="my-2">
                <h4 class="text-primary mb-0"><?= number_format($winner['preference_value'], 4) ?></h4>
                <small class="text-muted">Nilai Preferensi</small>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Tabel Matriks Keputusan (Raw) -->
        <h6 class="mb-3"><i class="fas fa-table"></i> Matriks Keputusan (Nilai Mentah)</h6>
        <div class="table-responsive mb-4">
          <table class="table table-bordered table-sm">
            <thead class="thead-dark">
              <tr>
                <th>Alternatif (Website)</th>
                <th class="text-center">C1: Analytics<br><small>Total Klik</small></th>
                <th class="text-center">C2: Konten<br><small>Jml Postingan</small></th>
                <th class="text-center">C3: Standarisasi<br><small>Elemen (/17)</small></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($results as $row): ?>
              <tr>
                <td>
                  <strong><?= esc($row['website_name']) ?></strong>
                  <span class="badge badge-light"><?= ucfirst($row['website_category'] ?? '') ?></span>
                </td>
                <td class="text-center"><?= number_format($row['raw_analytics']) ?></td>
                <td class="text-center"><?= number_format($row['raw_content']) ?></td>
                <td class="text-center"><?= $row['raw_standard'] ?>/17</td>
              </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot class="table-info">
              <tr>
                <td class="text-right"><strong>MAX</strong></td>
                <td class="text-center"><strong><?= number_format($maxValues['analytics']) ?></strong></td>
                <td class="text-center"><strong><?= number_format($maxValues['content']) ?></strong></td>
                <td class="text-center"><strong><?= $maxValues['standard'] ?></strong></td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- Tabel Normalisasi & Preferensi -->
        <h6 class="mb-3"><i class="fas fa-calculator"></i> Matriks Normalisasi & Nilai Preferensi (V)</h6>
        <div class="table-responsive mb-4">
          <table class="table table-bordered table-sm table-striped">
            <thead class="thead-dark">
              <tr>
                <th>Alternatif</th>
                <th class="text-center">R(C1)<br><small>Analytics</small></th>
                <th class="text-center">R(C2)<br><small>Konten</small></th>
                <th class="text-center">R(C3)<br><small>Standarisasi</small></th>
                <th class="text-center">V = Σ(W×R)<br><small>Preferensi</small></th>
                <th class="text-center">Peringkat</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($results as $row): ?>
              <tr class="<?= $row['rank'] <= 3 ? 'table-success' : '' ?>">
                <td><strong><?= esc($row['website_name']) ?></strong></td>
                <td class="text-center"><?= number_format($row['norm_analytics'], 4) ?></td>
                <td class="text-center"><?= number_format($row['norm_content'], 4) ?></td>
                <td class="text-center"><?= number_format($row['norm_standard'], 4) ?></td>
                <td class="text-center"><strong class="text-primary"><?= number_format($row['preference_value'], 4) ?></strong></td>
                <td class="text-center">
                  <?php if ($row['rank'] <= 3): ?>
                  <span class="badge <?= rankBadge($row['rank']) ?>">
                    <i class="fas fa-medal"></i> #<?= $row['rank'] ?>
                  </span>
                  <?php else: ?>
                  #<?= $row['rank'] ?>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Rumus SAW -->
        <div class="card bg-light border">
          <div class="card-body">
            <h6><i class="fas fa-info-circle text-primary"></i> Keterangan Metode SAW</h6>
            <ul class="mb-0">
              <li><strong>Normalisasi (Benefit):</strong> R<sub>ij</sub> = X<sub>ij</sub> / Max(X<sub>j</sub>)</li>
              <li><strong>Normalisasi (Cost):</strong> R<sub>ij</sub> = Min(X<sub>j</sub>) / X<sub>ij</sub></li>
              <li><strong>Nilai Preferensi:</strong> V<sub>i</sub> = Σ (W<sub>j</sub> × R<sub>ij</sub>)</li>
              <li><strong>Bobot:</strong>
                W<sub>analytics</sub> = <?= number_format($weights['analytics']['weight_value'] ?? 0, 4) ?>,
                W<sub>konten</sub> = <?= number_format($weights['content']['weight_value'] ?? 0, 4) ?>,
                W<sub>standarisasi</sub> = <?= number_format($weights['web_standardization']['weight_value'] ?? 0, 4) ?>
              </li>
              <li>Semua kriteria bertipe <strong>Benefit</strong> (semakin tinggi semakin baik)</li>
            </ul>
          </div>
        </div>

        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
