<?php

namespace App\Services\News;

use App\Contracts\Article\ArticleInterface;
use App\Contracts\Category\CategoryInterface;
use App\Contracts\Source\SourceInterface;
use App\Entities\Source\SourceEntity;
use Carbon\Carbon;

abstract class BaseService {

    protected string $serviceName = '';

    protected SourceInterface $sourceInterface;
    protected CategoryInterface $categoryInterface;

    protected ArticleInterface $articleInterface;

    private ?SourceEntity $source;

    public function __construct(SourceInterface $sourceInterface, CategoryInterface $categoryInterface, ArticleInterface $articleInterface) {
        $this->sourceInterface = $sourceInterface;
        $this->categoryInterface = $categoryInterface;
        $this->articleInterface = $articleInterface;
        $this->setSource();
    }

    private function setSource(): void {
        $this->source = $this->sourceInterface->getSourceByName($this->serviceName);
    }

    protected function getSourceInfo(): ?SourceEntity {
        return $this->source;
    }

    protected abstract function getStartDate(): string;

    protected function getCategories() {
        return $this->categoryInterface->getCategories()->keyBy(fn($c) => strtolower($c->name));
    }

    public abstract function getArticles(): void;

}
