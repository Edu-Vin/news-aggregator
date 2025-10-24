<?php

namespace App\Repositories\Article;

use App\Contracts\Article\ArticleInterface;
use App\Entities\Article\ArticleEntity;
use App\Repositories\BaseRepository;
use Illuminate\Container\Container as App;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ArticleRepository extends BaseRepository implements ArticleInterface {

    /**
     * @param App $app
     *
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    public function getArticles(array $requestQuery): LengthAwarePaginator {
        $articleQuery = $this->model->query();

        // Full-text search (title + description)
        if (isset( $requestQuery['search']) && $search = $requestQuery['search']) {
            $articleQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        // Filter by category (single or multiple)
        if (isset($requestQuery['category']) && $categories = $requestQuery['category']) {
            $articleQuery->whereIn('category_id', (array) $categories);
        }

        // Filter by source (single or multiple)
        if (isset($requestQuery['source']) && $sources = $requestQuery['source']) {
            $articleQuery->whereIn('source_id', (array) $sources);
        }

        // Filter by author
        if (isset($requestQuery['author']) && $author = $requestQuery['author']) {
            $articleQuery->where('author', 'like', "%$author%");
        }

        // Date filtering
        if (isset($requestQuery['from']) && $dateFrom = $requestQuery['from']) {
            $articleQuery->whereDate('published_at', '>=', $dateFrom);
        }

        if (isset($requestQuery['to']) && $dateTo = $requestQuery['to']) {
            $articleQuery->whereDate('published_at', '<=', $dateTo);
        }

        $articleQuery->with(['category', 'source']);

        $perPage = $requestQuery['per_page'] ?? 10;

        return $articleQuery->orderBy('published_at', 'desc')->paginate($perPage);
    }

    public function getAuthors(): Collection {
        return $this->model->select('author')
            ->whereNotNull('author')
            ->distinct()
            ->orderBy('author')
            ->pluck('author');
    }

    public function updateOrCreate(string $url, array $data): void {
        $this->model->updateOrCreate(['url' =>$url], $data);
    }

    protected function getClass(): string {
        return ArticleEntity::class;
    }
}
