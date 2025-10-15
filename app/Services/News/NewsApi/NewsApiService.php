<?php

namespace App\Services\News\NewsApi;

use App\Contracts\Article\ArticleInterface;
use App\Contracts\Category\CategoryInterface;
use App\Contracts\Source\SourceInterface;
use App\Services\News\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NewsApiService extends BaseService{

    protected string $serviceName = 'newsapi';

    protected SourceInterface $sourceInterface;
    protected CategoryInterface $categoryInterface;
    protected ArticleInterface $articleInterface;

    public function __construct(SourceInterface $sourceInterface, CategoryInterface $categoryInterface, ArticleInterface $articleInterface) {
        parent::__construct($sourceInterface, $categoryInterface, $articleInterface);
    }

    protected function getStartDate(): string {
        $source = $this->getSourceInfo();
        return $source->last_fetched_at
            ? Carbon::parse($source->last_fetched_at)->toIso8601String()
            : Carbon::parse('2025-10-01')->toIso8601String();
    }

    public function getArticles(): void {
        try {
            $source = $this->getSourceInfo();
            $page = 1;
            $pageSize = 10;
            $maxPages = 10;
            $totalPages = null;

            $categories = $this->getCategories();

            do {
                $response = Http::get(config("services.$this->serviceName.base_url")."/everything", [
                    'apiKey' => config("services.$this->serviceName.key"),
                    'from' => $this->getStartDate(),
                    'language' => 'en',
                    'sortBy' => 'publishedAt',
                    'pageSize' => $pageSize,
                    'page' => $page,
                    'domains' => "bbc.co.uk,techcrunch.com,engadget.com,wired.com,theverge.com"
                ]);

                if (!$response->successful()) {
                    Log::warning('NewsAPI failed');
                    Log::warning($response->json());
                    break;
                }

                $data = $response->json();

                if (is_null($totalPages)) {
                    $totalResults = $data['totalResults'] ?? 0;
                    $totalPages = min(ceil($totalResults / $pageSize), $maxPages);
                }

                $articles = $data['articles'] ?? [];

                foreach ($articles as $article) {
                    $title = $article['title'] ?? '';
                    $url = $article['url'] ?? '';
                    $description = $article['description'] ?? '';
                    $content = $article['content'] ?? '';
                    $fullText = strtolower("{$title} {$description} {$content}");

                    // Match category by keyword in text
                    $matchedCategory = $categories->first(function ($category) use ($fullText) {
                        return Str::contains($fullText, strtolower($category->name)) || Str::contains('general', strtolower($category->name));
                    });

                    if (!$url || !$title) {
                        continue;
                    }
                    $this->articleInterface->updateOrCreate($article['url'], [
                        'title' => $title,
                        'description' => $description,
                        'author' => $article['author'] ?? null,
                        'published_at' => $article['publishedAt'] ?? now(),
                        'category_id' => optional($matchedCategory)->id,
                        'source_id' => $source->id,
                    ]);
                }

                $page++;
            } while ($page <= $totalPages);

            $source->update(['last_fetched_at' => now()]);
        }catch (\Throwable $e) {
            Log::critical("API error from {$this->serviceName}: " . $e->getMessage());
        }
    }
}
