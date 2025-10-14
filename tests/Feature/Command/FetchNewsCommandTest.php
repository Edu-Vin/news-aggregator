<?php

namespace Tests\Feature\Command;

use App\Jobs\FetchNewsSourceJob;
use App\Services\News\Guardian\GuardianService;
use App\Services\News\NewsApi\NewsApiService;
use App\Services\News\NYtimes\NYTimesService;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class FetchNewsCommandTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_dispatches_fetch_jobs_for_all_configured_news_sources(): void
    {
        Bus::fake();

        // Act: Run the command
        $this->artisan('news:fetch')
            ->expectsOutput("Dispatched: " . NewsApiService::class)
            ->expectsOutput("Dispatched: " . GuardianService::class)
            ->expectsOutput("Dispatched: " . NYTimesService::class)
            ->assertSuccessful();

        // Assert: Each source service was dispatched in its own job
        Bus::assertDispatched(FetchNewsSourceJob::class, function ($job) {
            return $job->sourceClass === NewsApiService::class;
        });

        Bus::assertDispatched(FetchNewsSourceJob::class, function ($job) {
            return $job->sourceClass === GuardianService::class;
        });

        Bus::assertDispatched(FetchNewsSourceJob::class, function ($job) {
            return $job->sourceClass === NYTimesService::class;
        });

        // Optional: Ensure no other jobs dispatched
        Bus::assertDispatchedTimes(FetchNewsSourceJob::class, 3);
    }
}
