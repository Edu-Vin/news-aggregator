<?php

namespace App\Http\Controllers\User;

use App\Contracts\User\UserPreferenceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserPreference\SinglePreferenceRequest;
use App\Http\Requests\UserPreference\UpdatePreferencesRequest;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\UserPreferenceResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    //
    protected UserPreferenceInterface $userPreferenceInterface;

    public function __construct(UserPreferenceInterface $userPreferenceInterface) {
        $this->userPreferenceInterface = $userPreferenceInterface;
    }

    /**
     * Get user preferences
     */
    public function index(Request $request): JsonResponse
    {
        $preferences = $this->userPreferenceInterface->getUserPreferences($request->user());

        return response()->json([
            "status" => "success",
            'data' => new UserPreferenceResource($preferences),
        ]);
    }

    /**
     * Update all preferences at once
     */
    public function update(UpdatePreferencesRequest $request): JsonResponse
    {
        $this->userPreferenceInterface->updatePreferences(
            $request->user(),
            $request->validated()
        );

        $preferences = $this->userPreferenceInterface->getUserPreferences($request->user());

        return response()->json([
            "status" => "success",
            'message' => 'Preferences updated successfully',
            'data' => new UserPreferenceResource($preferences),
        ]);
    }

    /**
     * Add a single preference item
     */
    public function addItem(SinglePreferenceRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($request->has('source_id')) {
            $this->userPreferenceInterface->addPreferredSource($user, $request->source_id);
        }

        if ($request->has('category_id')) {
            $this->userPreferenceInterface->addPreferredCategory($user, $request->category_id);
        }

        if ($request->has('author_name')) {
            $this->userPreferenceInterface->addPreferredAuthor($user, $request->author_name);
        }

        return response()->json([
            'status' => "success",
            'message' => 'Preference added successfully',
        ]);
    }

    /**
     * Remove a single preference item
     */
    public function removeItem(SinglePreferenceRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($request->has('source_id')) {
            $this->userPreferenceInterface->removePreferredSource($user, $request->source_id);
        }

        if ($request->has('category_id')) {
            $this->userPreferenceInterface->removePreferredCategory($user, $request->category_id);
        }

        if ($request->has('author_name')) {
            $this->userPreferenceInterface->removePreferredAuthor($user, $request->author_name);
        }

        return response()->json([
            'status' => "success",
            'message' => 'Preference removed successfully',
        ]);
    }

    /**
     * Get personalized feed based on preferences
     */
    public function feed(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $articles = $this->userPreferenceInterface->getPersonalizedArticles(
            $request->user(),
            $filters
        );

        return response()->json([
            'data' => ArticleResource::collection($articles),
            'meta' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
            ],
        ]);
    }

}
