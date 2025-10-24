<?php

namespace App\Jobs;

use App\Factories\NewsSourceFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class FetchNewsSourceJob implements ShouldQueue
{
    use Queueable;

    public string $sourceClass;

    /**
     * Create a new job instance.
     */
    public function __construct(string $sourceClass)
    {
        //
        $this->sourceClass = $sourceClass;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $source = NewsSourceFactory::make($this->sourceClass);

        logger()->info("Fetching news from: {$this->sourceClass}");
        $source->getArticles();
        logger()->info("Completed: {$this->sourceClass}");
    }

    public function failed(Throwable $exception): void
    {
        logger()->error("Fetch failed for {$this->sourceClass}", [
            'error' => $exception->getMessage(),
        ]);
    }
}
