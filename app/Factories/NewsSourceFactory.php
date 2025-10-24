<?php

namespace App\Factories;

use App\Services\News\BaseService;
use App\Services\News\Guardian\GuardianService;
use App\Services\News\NewsApi\NewsApiService;
use App\Services\News\NYtimes\NYTimesService;
use InvalidArgumentException;

class NewsSourceFactory
{
    public static function make(string $type): BaseService {
        return match ($type) {
            GuardianService::class => app(GuardianService::class),
            NewsApiService::class => app(NewsApiService::class),
            NYTimesService::class => app(NYTimesService::class),
            default => throw new InvalidArgumentException("Unknown news source type: {$type}"),
        };
    }

}
