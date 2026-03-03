<?php

namespace App\Models;

use CodeIgniter\Model;

class TopArticleModel extends Model
{
    protected $table         = 'tr_top_articles';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['performance_id', 'article_title', 'article_clicks', 'rank'];
    protected $useTimestamps = true;
    protected $returnType    = 'array';

    /**
     * Ambil artikel berdasarkan performance_id
     */
    public function getByPerformanceId($performanceId)
    {
        return $this->where('performance_id', $performanceId)
            ->orderBy('rank', 'ASC')
            ->findAll();
    }

    /**
     * Ambil semua top articles untuk periode tertentu (leaderboard)
     */
    public function getLeaderboard($periodId = null, $websiteId = null)
    {
        $builder = $this->select('tr_top_articles.*, ms_websites.website_name, ms_websites.category as website_category, ms_periods.period_name')
            ->join('tr_performance', 'tr_performance.id = tr_top_articles.performance_id')
            ->join('ms_websites', 'ms_websites.id = tr_performance.website_id')
            ->join('ms_periods', 'ms_periods.id = tr_performance.period_id');

        if ($periodId) {
            $builder->where('tr_performance.period_id', $periodId);
        }

        if ($websiteId) {
            $builder->where('tr_performance.website_id', $websiteId);
        }

        return $builder->orderBy('tr_top_articles.article_clicks', 'DESC')
            ->findAll();
    }

    /**
     * Simpan batch artikel (hapus lama lalu insert baru)
     */
    public function saveBatch($performanceId, array $articles)
    {
        // Hapus artikel lama
        $this->where('performance_id', $performanceId)->delete();

        // Insert artikel baru
        foreach ($articles as $rank => $article) {
            if (!empty($article['title'])) {
                $this->insert([
                    'performance_id' => $performanceId,
                    'article_title'  => $article['title'],
                    'article_clicks' => (int) ($article['clicks'] ?? 0),
                    'rank'           => $rank + 1,
                ]);
            }
        }
    }
}
