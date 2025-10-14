<?php

namespace App\Services\News\Guardian;

use App\Contracts\Article\ArticleInterface;
use App\Contracts\Category\CategoryInterface;
use App\Contracts\Source\SourceInterface;
use App\Services\News\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GuardianService extends BaseService {

    protected string $serviceName = 'guardian';

    protected SourceInterface $sourceInterface;
    protected CategoryInterface $categoryInterface;
    protected ArticleInterface $articleInterface;

    public function __construct(SourceInterface $sourceInterface, CategoryInterface $categoryInterface, ArticleInterface $articleInterface) {
        parent::__construct($sourceInterface, $categoryInterface, $articleInterface);
    }

    protected function getStartDate(): string {
        $source = $this->getSourceInfo();
        return $source->last_fetched_at
            ? Carbon::parse($source->last_fetched_at)->toDateString()
            : Carbon::parse('2025-10-01')->toDateString();
    }

    public function getArticles(): void {
        try {
            $source = $this->getSourceInfo();
            $page = 1;
            $totalPages = 1;

            $categories = $this->getCategories();

            while ($page <= $totalPages) {
                $response = Http::get(config("services.$this->serviceName.base_url").'/search', [
                    'api-key' => config("services.$this->serviceName.key"),
                    'from-date' => $this->getStartDate(),
                    'page' => $page,
                    'page-size' => 50,
                    'show-fields' => 'trailText,byline,thumbnail',
                    'order-by' => 'newest',
                ]);

                if (!$response->successful()) {
                    Log::warning('Guardian API failed');
                    Log::warning($response->json());
                    break;
                }

                $data = $response->json();
                $articles = $data['response']['results'] ?? [];
                $totalPages = $data['response']['pages'];


                foreach ($articles as $article) {
                    $sectionName = strtolower($article['sectionName'] ?? '');
                    $url = $article['webUrl'];
                    $title = $article['webTitle'];

                    if (!$categories->has($sectionName) || !$url || !$title) {
                        continue;
                    }

                    $category = $categories->get($sectionName);
                    $this->articleInterface->updateOrCreate($article['webUrl'], [
                        'title' => $article['webTitle'],
                        'description' => null,
                        'author' => null,
                        'published_at' => $article['webPublicationDate'],
                        'category_id' => $category->id,
                        'source_id' => $source->id,
                    ]);
                }

                $page++;
            }

            $source->update(['last_fetched_at' => now()]);
        } catch (\Throwable $e) {
            Log::critical("API error from {$this->serviceName}: " . $e->getMessage());
        }

    }
}
