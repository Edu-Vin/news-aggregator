<?php

namespace App\Entities\Article;

use App\Entities\Category\CategoryEntity;
use App\Entities\Source\SourceEntity;
use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleEntity extends Model {

    use HasFactory;

    protected $table = 'articles';

    protected $guarded = [];

    public function category(): BelongsTo {
        return $this->belongsTo(CategoryEntity::class);
    }

    public function source(): BelongsTo {
        return $this->belongsTo(SourceEntity::class);
    }

    protected static function newFactory(): ArticleFactory
    {
        return ArticleFactory::new();
    }
}
