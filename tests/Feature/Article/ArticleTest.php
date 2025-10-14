<?php

namespace Tests\Feature\Article;

use App\Entities\Article\ArticleEntity;
use App\Entities\Category\CategoryEntity;
use App\Entities\Source\SourceEntity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed some categories and sources
        CategoryEntity::factory()->create(['name' => 'Technology']);
        CategoryEntity::factory()->create(['name' => 'Health']);

        SourceEntity::factory()->create(['name' => 'BBC']);
        SourceEntity::factory()->create(['name' => 'NYTimes']);
    }

    public function test_returns_paginated_articles()
    {
        $category = CategoryEntity::first();
        $source = SourceEntity::first();

        ArticleEntity::factory()->count(15)->create([
            'category_id' => $category->id,
            'source_id' => $source->id,
        ]);

        $response = $this->getJson('/api/articles');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'title', 'description', 'author', 'published_at', 'category', 'source',
                    ],
                ],
                'links',
                'meta',
            ]);

        // Default pagination is 10 per page
        $this->assertCount(10, $response->json('data'));
    }

    public function test_can_filter_articles_by_source()
    {
        $category = CategoryEntity::first();
        $bbcSource = SourceEntity::where('name', 'BBC')->first();
        $nytSource = SourceEntity::where('name', 'NYTimes')->first();

        // Articles in different categories
        ArticleEntity::factory()->create(['category_id' => $category->id, 'source_id' => $bbcSource->id, 'title' => 'Tech news']);
        ArticleEntity::factory()->create(['category_id' => $category->id, 'source_id' => $nytSource->id, 'title' => 'Health news']);

        $response = $this->getJson('/api/articles?source=' . $bbcSource->id);

        $response->assertOk();
        $articles = $response->json('data');

        $this->assertCount(1, $articles);
        $this->assertEquals('Tech news', $articles[0]['title']);
    }

    public function test_can_filter_articles_by_category()
    {
        $techCategory = CategoryEntity::where('name', 'Technology')->first();
        $healthCategory = CategoryEntity::where('name', 'Health')->first();
        $source = SourceEntity::first();

        // Articles in different categories
        ArticleEntity::factory()->create(['category_id' => $techCategory->id, 'source_id' => $source->id, 'title' => 'Tech news']);
        ArticleEntity::factory()->create(['category_id' => $healthCategory->id, 'source_id' => $source->id, 'title' => 'Health news']);

        $response = $this->getJson('/api/articles?category=' . $techCategory->id);

        $response->assertOk();
        $articles = $response->json('data');

        $this->assertCount(1, $articles);
        $this->assertEquals('Tech news', $articles[0]['title']);
    }
}
