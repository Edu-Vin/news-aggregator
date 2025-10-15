<?php

namespace App\Repositories\User;

use App\Contracts\User\UserPreferenceInterface;
use App\Entities\Article\ArticleEntity;
use App\Entities\Category\CategoryEntity;
use App\Entities\Source\SourceEntity;
use App\Entities\User\UserEntity;
use App\Entities\User\UserPreferenceEntity;
use App\Repositories\BaseRepository;
use Illuminate\Container\Container as App;
use Illuminate\Pagination\LengthAwarePaginator;

class UserPreferenceRepository extends BaseRepository implements UserPreferenceInterface {

    /**
     * @param App $app
     *
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    public function getUserPreferences(UserEntity $user): ?array
    {
        $preference = $user->preference;

        if (!$preference) {
            return [
                'sources' => [],
                'categories' => [],
                'authors' => [],
            ];
        }

        // Fetch actual source and category names
        $sourceIds = $preference->getSourceIds();
        $categoryIds = $preference->getCategoryIds();

        $sources = SourceEntity::whereIn('id', $sourceIds)->get()->map(fn($s) => [
            'id' => $s->id,
            'name' => $s->name,
        ])->toArray();

        $categories = CategoryEntity::whereIn('id', $categoryIds)->get()->map(fn($c) => [
            'id' => $c->id,
            'name' => $c->name,
        ])->toArray();

        return [
            'sources' => $sources,
            'categories' => $categories,
            'authors' => $preference->getAuthorNames(),
        ];
    }

    /**
     * Update all preferences at once
     */
    public function updatePreferences(UserEntity $user, array $data): void
    {
        $user->preference()->update($data);
    }

    /**
     * Add preferred source
     */
    public function addPreferredSource(UserEntity $user, int $sourceId): void
    {
        $preference = $user->getOrCreatePreference();
        $sources = $preference->getSourceIds();
        if (!in_array($sourceId, $sources)) {
            $sources[] = $sourceId;
            $preference->update(['sources' => $sources]);
        }
    }

    /**
     * Remove preferred source
     */
    public function removePreferredSource(UserEntity $user, int $sourceId): void
    {
        $preference = $user->preference;
        if ($preference) {
            $sources = array_diff($preference->getSourceIds(), [$sourceId]);
            $preference->update(['sources' => array_values($sources)]);
        }
    }

    public function addPreferredCategory(UserEntity $user, int $categoryId): void
    {
        $preference = $user->getOrCreatePreference();
        $categories = $preference->getCategoryIds();
        if (!in_array($categoryId, $categories)) {
            $categories[] = $categoryId;
            $preference->update(['categories' => $categories]);
        }
    }

    /**
     * Remove preferred category
     */
    public function removePreferredCategory(UserEntity $user, int $categoryId): void
    {
        $preference = $user->preference;
        if ($preference) {
            $categories = array_diff($preference->getCategoryIds(), [$categoryId]);
            $preference->update(['categories' => array_values($categories)]);
        }
    }

    /**
     * Add preferred author
     */
    public function addPreferredAuthor(UserEntity $user, string $authorName): void
    {
        $preference = $user->getOrCreatePreference();
        $authors = $preference->getAuthorNames();
        if (!in_array($authorName, $authors)) {
            $authors[] = $authorName;
            $preference->update(['authors' => $authors]);
        }
    }

    /**
     * Remove preferred author
     */
    public function removePreferredAuthor(UserEntity $user, string $authorName): void
    {
        $preference = $user->preference;
        if ($preference) {
            $authors = array_diff($preference->getAuthorNames(), [$authorName]);
            $preference->update(['authors' => array_values($authors)]);
        }
    }

    /**
     * Get personalized articles based on user preferences
     */
    public function getPersonalizedArticles(UserEntity $user, array $filters = []): LengthAwarePaginator
    {
        $query = ArticleEntity::query()->with(['source', 'category']);

        $preference = $user->preference;

        // If no preferences, return all articles
        if (!$preference->hasAnyPreferences()) {
            return $query->orderBy('published_at', 'desc')
                ->paginate($filters['per_page'] ?? 10);
        }

        // Get preference arrays
        $preferredSourceIds = $preference->getSourceIds();
        $preferredCategoryIds = $preference->getCategoryIds();
        $preferredAuthors = $preference->getAuthorNames();

        // Filter by preferences
        $query->where(function ($q) use ($preferredSourceIds, $preferredCategoryIds, $preferredAuthors) {
            if (!empty($preferredSourceIds)) {
                $q->orWhereIn('source_id', $preferredSourceIds);
            }

            if (!empty($preferredCategoryIds)) {
                $q->orWhereIn('category_id', $preferredCategoryIds);
            }

            if (!empty($preferredAuthors)) {
                $q->orWhereIn('author', $preferredAuthors);
            }
        });

        // Apply additional filters
        $this->applyFilters($query, $filters);

        $query->orderBy('published_at', 'desc');

        return $query->paginate($filters['per_page'] ?? 15);
    }

    private function applyFilters($query, array $filters): void
    {
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['from'])) {
            $query->whereDate('published_at', '>=', $filters['from']);
        }

        if (isset($filters['to'])) {
            $query->whereDate('published_at', '<=', $filters['to']);
        }
    }

    protected function getClass(): string {
        return UserPreferenceEntity::class;
    }
}
