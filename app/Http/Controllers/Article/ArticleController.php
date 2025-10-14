<?php

namespace App\Http\Controllers\Article;

use App\Contracts\Article\ArticleInterface;
use App\Http\Controllers\Controller;
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

    public function index(Request $request): ResourceCollection {
        $articles = $this->articleInterface->getArticles($request->query());
        return ArticleResource::collection($articles);
    }

    public function getAuthors(): JsonResponse {
        return response()->json([
            'data' => $this->articleInterface->getAuthors()
        ]);
    }
}
