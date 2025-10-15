<?php

namespace App\Entities\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreferenceEntity extends Model {

    use HasFactory;

    protected $table = 'user_preferences';

    protected $fillable = [
        'user_id',
        'sources',
        'categories',
        'authors',
    ];

    protected $casts = [
        'sources' => 'array',
        'categories' => 'array',
        'authors' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserEntity::class, 'user_id', 'id');
    }

    /**
     * Get source IDs as array
     */
    public function getSourceIds(): array
    {
        return $this->sources ?? [];
    }

    /**
     * Get category IDs as array
     */
    public function getCategoryIds(): array
    {
        return $this->categories ?? [];
    }

    /**
     * Get author names as array
     */
    public function getAuthorNames(): array
    {
        return $this->authors ?? [];
    }

    /**
     * Check if any preferences are set
     */
    public function hasAnyPreferences(): bool
    {
        return !empty($this->sources)
            || !empty($this->categories)
            || !empty($this->authors);
    }
}
