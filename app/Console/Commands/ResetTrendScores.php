<?php

namespace App\Console\Commands;

use App\Models\News;
use Illuminate\Console\Command;

class ResetTrendScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-trend-scores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trend skorlarını günlük sıfırlar ve optimize eder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Trend skorları güncelleniyor...');

        News::query()->chunk(100, function ($newsList) {

            foreach ($newsList as $news) {

                $newDailyViews = intval($news->daily_views * 0.30);

                $newWeeklyViews = intval($news->weekly_views * 0.85);

                $newMonthlyViews = intval($news->monthly_views * 0.97);

                $trendScore =
                    ($newDailyViews * 5) +
                    ($newWeeklyViews * 2) +
                    $newMonthlyViews;

                $news->update([

                    'daily_views' => $newDailyViews,

                    'weekly_views' => $newWeeklyViews,

                    'monthly_views' => $newMonthlyViews,

                    'trend_score' => $trendScore,

                    'is_trending' => $trendScore >= 50,

                ]);
            }
        });

        $this->info('Trend sistemi başarıyla optimize edildi.');

        return Command::SUCCESS;
    }
}