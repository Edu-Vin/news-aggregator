<?php

namespace App\Repositories\Category;

use App\Contracts\Category\CategoryInterface;
use App\Entities\Category\CategoryEntity;
use App\Repositories\BaseRepository;
use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository extends BaseRepository implements CategoryInterface {

    /**
     * @param App $app
     *
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    public function getCategories(): Collection {
        return $this->model->select('id', 'name')->orderBy('name')->get();
    }

    protected function getClass(): string {
        return CategoryEntity::class;
    }
}
