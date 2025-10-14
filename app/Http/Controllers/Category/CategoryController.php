<?php

namespace App\Http\Controllers\Category;

use App\Contracts\Category\CategoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    protected CategoryInterface $categoryInterface;
    public function __construct(CategoryInterface $categoryInterface) {
        $this->categoryInterface = $categoryInterface;
    }

    public function index(): JsonResponse{
        return response()->json([
            'data' => $this->categoryInterface->getCategories()
        ]);
    }
}
