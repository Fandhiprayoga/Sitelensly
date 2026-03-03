<?php

namespace App\Models;

use CodeIgniter\Model;

class AwardingScoreModel extends Model
{
    protected $table         = 'tr_awarding_scores';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'awarding_period_id', 'website_id',
        'clicks_web', 'clicks_mobile', 'clicks_tablet', 'total_posts',
        'std_banner', 'std_greeting', 'std_hot_news', 'std_facilities',
        'std_graduated_testimony', 'std_about_program', 'std_vision_mission',
        'std_organization', 'std_accreditation', 'std_academic_staff',
        'std_curriculum', 'std_career_prospect', 'std_title_graduation',
        'std_learning_outcome', 'std_research', 'std_news', 'std_admission',
        'is_imported', 'input_by', 'notes',
    ];
    protected $useTimestamps = true;
    protected $returnType    = 'array';

    /**
     * Daftar elemen standarisasi web (17 elemen)
     */
    public static function getStandardizationElements(): array
    {
        return [
            'std_banner'              => 'Banner',
            'std_greeting'            => 'Greeting',
            'std_hot_news'            => 'Hot News',
            'std_facilities'          => 'Facilities',
            'std_graduated_testimony' => 'Graduated Testimony',
            'std_about_program'       => 'About the Program',
            'std_vision_mission'      => 'Vision & Mission',
            'std_organization'        => 'Organization',
            'std_accreditation'       => 'Accreditation',
            'std_academic_staff'      => 'Academic Staff',
            'std_curriculum'          => 'Curriculum',
            'std_career_prospect'     => 'Career Prospect',
            'std_title_graduation'    => 'Title of Graduation',
            'std_learning_outcome'    => 'Learning Outcome',
            'std_research'            => 'Research',
            'std_news'                => 'News',
            'std_admission'           => 'Admission',
        ];
    }

    /**
     * Ambil skor per periode awarding dengan relasi website
     */
    public function getScoresWithWebsite(int $awardingPeriodId): array
    {
        return $this->select('tr_awarding_scores.*, ms_websites.website_name, ms_websites.category as website_category, ms_websites.url')
            ->join('ms_websites', 'ms_websites.id = tr_awarding_scores.website_id')
            ->where('tr_awarding_scores.awarding_period_id', $awardingPeriodId)
            ->orderBy('ms_websites.website_name', 'ASC')
            ->findAll();
    }

    /**
     * Ambil skor untuk website tertentu di periode tertentu
     */
    public function getByWebsiteAndPeriod(int $websiteId, int $awardingPeriodId)
    {
        return $this->where('website_id', $websiteId)
            ->where('awarding_period_id', $awardingPeriodId)
            ->first();
    }

    /**
     * Hitung skor analytics (total klik semua perangkat)
     */
    public function calcAnalyticsScore(array $score): int
    {
        return (int)($score['clicks_web'] + $score['clicks_mobile'] + $score['clicks_tablet']);
    }

    /**
     * Hitung skor konten (jumlah postingan)
     */
    public function calcContentScore(array $score): int
    {
        return (int)$score['total_posts'];
    }

    /**
     * Hitung skor standarisasi web (jumlah elemen yang terpenuhi dari 17)
     */
    public function calcStandardizationScore(array $score): int
    {
        $elements = self::getStandardizationElements();
        $count = 0;
        foreach (array_keys($elements) as $field) {
            if (!empty($score[$field])) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Hitung SAW (Simple Additive Weighting)
     *
     * Steps:
     * 1. Build decision matrix X (alternatives × 3 criteria)
     * 2. Normalize: r_ij = x_ij / max(x_j) for benefit, min(x_j) / x_ij for cost
     * 3. Preference value: V_i = Σ(w_j × r_ij)
     * 4. Rank by V_i descending
     */
    public function calculateSAW(int $awardingPeriodId, array $weights): array
    {
        $scores = $this->getScoresWithWebsite($awardingPeriodId);

        if (empty($scores) || empty($weights)) {
            return [];
        }

        // Step 1: Build decision matrix
        $matrix = [];
        foreach ($scores as $score) {
            $matrix[] = [
                'id'              => $score['id'],
                'website_id'      => $score['website_id'],
                'website_name'    => $score['website_name'],
                'website_category'=> $score['website_category'],
                'url'             => $score['url'],
                'raw_analytics'   => $this->calcAnalyticsScore($score),
                'raw_content'     => $this->calcContentScore($score),
                'raw_standard'    => $this->calcStandardizationScore($score),
                'score_data'      => $score,
            ];
        }

        // Step 2: Find max/min for normalization
        $maxAnalytics = max(array_column($matrix, 'raw_analytics')) ?: 1;
        $maxContent   = max(array_column($matrix, 'raw_content')) ?: 1;
        $maxStandard  = max(array_column($matrix, 'raw_standard')) ?: 1;

        // Determine weight values
        $wAnalytics = (float)($weights['analytics']['weight_value'] ?? 0);
        $wContent   = (float)($weights['content']['weight_value'] ?? 0);
        $wStandard  = (float)($weights['web_standardization']['weight_value'] ?? 0);

        // Determine criteria types
        $tAnalytics = $weights['analytics']['criteria_type'] ?? 'benefit';
        $tContent   = $weights['content']['criteria_type'] ?? 'benefit';
        $tStandard  = $weights['web_standardization']['criteria_type'] ?? 'benefit';

        $minAnalytics = min(array_column($matrix, 'raw_analytics')) ?: 0;
        $minContent   = min(array_column($matrix, 'raw_content')) ?: 0;
        $minStandard  = min(array_column($matrix, 'raw_standard')) ?: 0;

        // Step 3: Normalize and calculate preference values
        foreach ($matrix as &$row) {
            // Normalize per criteria type
            if ($tAnalytics === 'benefit') {
                $row['norm_analytics'] = $row['raw_analytics'] / $maxAnalytics;
            } else {
                $row['norm_analytics'] = $row['raw_analytics'] > 0 ? $minAnalytics / $row['raw_analytics'] : 0;
            }

            if ($tContent === 'benefit') {
                $row['norm_content'] = $row['raw_content'] / $maxContent;
            } else {
                $row['norm_content'] = $row['raw_content'] > 0 ? $minContent / $row['raw_content'] : 0;
            }

            if ($tStandard === 'benefit') {
                $row['norm_standard'] = $row['raw_standard'] / $maxStandard;
            } else {
                $row['norm_standard'] = $row['raw_standard'] > 0 ? $minStandard / $row['raw_standard'] : 0;
            }

            // Step 4: Preference value V_i = Σ(w_j × r_ij)
            $row['preference_value'] = round(
                ($wAnalytics * $row['norm_analytics']) +
                ($wContent   * $row['norm_content']) +
                ($wStandard  * $row['norm_standard']),
                4
            );
        }
        unset($row);

        // Step 5: Sort by preference value descending
        usort($matrix, fn($a, $b) => $b['preference_value'] <=> $a['preference_value']);

        // Add rank
        $rank = 1;
        foreach ($matrix as &$row) {
            $row['rank'] = $rank++;
        }

        return $matrix;
    }
}
