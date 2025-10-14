<?php

namespace App\Contracts\Article;

interface ArticleInterface {

    public function getArticles(array $requestQuery);

    public function getAuthors();
    public function updateOrCreate(string $url, array $data): void;
}
