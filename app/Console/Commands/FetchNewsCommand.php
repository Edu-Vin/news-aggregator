<?php

namespace App\Console\Commands;

use App\Jobs\FetchNewsSourceJob;
use App\Services\News\Guardian\GuardianService;
use App\Services\News\NewsApi\NewsApiService;
use App\Services\News\NYtimes\NYTimesService;
use Illuminate\Console\Command;

class FetchNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get news articles from all sources';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sources = [
            NewsApiService::class,
            GuardianService::class,
            NYTimesService::class
        ];

        foreach ($sources as $source) {
            FetchNewsSourceJob::dispatch($source);
            $this->info("Dispatched: {$source}");
        }

        return parent::SUCCESS;
    }
}
