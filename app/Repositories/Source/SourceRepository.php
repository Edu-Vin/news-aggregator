<?php

namespace App\Repositories\Source;

use App\Contracts\Source\SourceInterface;
use App\Entities\Source\SourceEntity;
use App\Repositories\BaseRepository;
use Illuminate\Container\Container as App;

class SourceRepository extends BaseRepository implements SourceInterface {

    /**
     * @param App $app
     *
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    public function getSources() {
        return $this->model->select('id', 'name')->get();
    }
    public function getSourceByName(string $name): ?SourceEntity {
        return $this->model->where('name', $name)->first();
    }

    protected function getClass(): string {
        return SourceEntity::class;
    }
}
