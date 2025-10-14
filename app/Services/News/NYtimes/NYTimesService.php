<?php

namespace App\Services\News\NYtimes;

use App\Contracts\Article\ArticleInterface;
use App\Contracts\Category\CategoryInterface;
use App\Contracts\Source\SourceInterface;
use App\Services\News\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NYTimesService extends BaseService {

    protected string $serviceName = 'nytimes';

    protected SourceInterface $sourceInterface;
    protected CategoryInterface $categoryInterface;
    protected ArticleInterface $articleInterface;

    public function __construct(SourceInterface $sourceInterface, CategoryInterface $categoryInterface, ArticleInterface $articleInterface) {
        parent::__construct($sourceInterface, $categoryInterface, $articleInterface);
    }

    protected function getStartDate(): string {
        $source = $this->getSourceInfo();
        return $source->last_fetched_at
            ? Carbon::parse($source->last_fetched_at)->format('Ymd')
            : Carbon::parse('2025-10-01')->format('Ymd');
    }

    public function getArticles(): void {
        try {
            $source = $this->getSourceInfo();

            $page = 0;
            $articlesPerPage = 10;
            $maxPages = 100;
            $totalHits = null;

            $categories = $this->getCategories();

            while ($page < $maxPages) {
                $response = Http::get(config("services.$this->serviceName.base_url").'/svc/search/v2/articlesearch.json', [
                    'api-key' => config("services.$this->serviceName.key"),
                    'begin_date' => $this->getStartDate(),
                    'sort' => 'newest',
                    'page' => $page,
                ]);

                if (!$response->successful()) {
                    Log::warning('NYTimes API failed');
                    Log::warning($response->json());
                    continue;
                }

                $data = $response->json();
                $articles = $response['response']['docs'] ?? [];
                $metadata = $data['response']['metadata'] ?? [];

                // Initialize totalHits
                if ($totalHits === null && isset($metadata['hits'])) {
                    $totalHits = min($metadata['hits'], 1000); // NYT max cap
                    $maxPages = ceil($totalHits / $articlesPerPage);
                }

                foreach ($articles as $doc) {
                    $url = $doc['web_url'] ?? null;
                    $title = $doc['headline']['main'] ?? null;
                    $description = $doc['abstract'] ?? null;
                    $publishedAt = $doc['pub_date'] ?? null;
                    $section = strtolower($doc['section_name'] ?? '');

                    if (!$categories->has($section) || !$url || !$title) {
                        continue; // skip invalid
                    }

                    $category = $categories->get($section); // match by section name
                    $this->articleInterface->updateOrCreate($url, [
                        'title' => $title,
                        'description' => $description,
                        'author' => $doc['byline']['original'] ?? null,
                        'published_at' => $publishedAt,
                        'category_id' => optional($category)->id,
                        'source_id' => $source->id,
                    ]);

                }

                $page++;
            }

            $source->update(['last_fetched_at' => now()]);
        }catch (\Throwable $e) {
            Log::critical("API error from {$this->serviceName}: " . $e->getMessage());
        }
    }
}
