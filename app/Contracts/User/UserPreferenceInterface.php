<?php

namespace App\Contracts\User;

use App\Entities\User\UserEntity;

interface UserPreferenceInterface {

    public function getUserPreferences(UserEntity $user): ?array;

    public function updatePreferences(UserEntity $user, array $data): void;

    public function addPreferredSource(UserEntity $user, int $sourceId): void;

    public function removePreferredSource(UserEntity $user, int $sourceId): void;

    public function addPreferredCategory(UserEntity $user, int $categoryId): void;

    public function removePreferredCategory(UserEntity $user, int $categoryId): void;

    public function addPreferredAuthor(UserEntity $user, string $authorName): void;

    public function removePreferredAuthor(UserEntity $user, string $authorName): void;

    public function getPersonalizedArticles(UserEntity $user, array $filters = []): mixed;
}
