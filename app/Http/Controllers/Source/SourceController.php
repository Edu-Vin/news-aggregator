<?php

namespace App\Http\Controllers\Source;

use App\Contracts\Category\CategoryInterface;
use App\Contracts\Source\SourceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SourceController extends Controller
{
    protected SourceInterface $sourceInterface;
    public function __construct(SourceInterface $sourceInterface) {
        $this->sourceInterface = $sourceInterface;
    }

    public function index(): JsonResponse{
        return response()->json([
            'data' => $this->sourceInterface->getSources()
        ]);
    }
}
