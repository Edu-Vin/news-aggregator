<?php

namespace App\Entities\Source;

use App\Entities\Article\ArticleEntity;
use Database\Factories\SourceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SourceEntity extends Model {

    use HasFactory;

    protected $table = 'sources';

    protected $fillable = ['name', 'last_fetched_at'];

    public function articles(): HasMany {
        return $this->hasMany(ArticleEntity::class);
    }

    protected static function newFactory(): SourceFactory
    {
        return SourceFactory::new();
    }

}
