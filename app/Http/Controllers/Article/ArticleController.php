<?php

namespace App\Http\Controllers\Article;

use App\Contracts\Article\ArticleInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleSearchRequest;
use App\Http\Resources\ArticleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleController extends Controller
{

    private ArticleInterface $articleInterface;

    public function __construct(ArticleInterface $articleInterface) {
        $this->articleInterface = $articleInterface;
    }

    /**
     * Get all articles
     */
    public function index(ArticleSearchRequest $request): JsonResponse {
        $articles = $this->articleInterface->getArticles($request->validated());
        return response()->json([
            'status' => 'success',
            'data' => ArticleResource::collection($articles),
            'meta' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
            ],
        ]);
    }

    /**
     * Get all authors
     */
    public function getAuthors(): JsonResponse {
        return response()->json([
            'status' => 'success',
            'data' => $this->articleInterface->getAuthors()
        ]);
    }
}
