<?php

namespace App\Repositories;

use Illuminate\Container\Container;
use Illuminate\Container\Container as App;

abstract class BaseRepository {

    protected Container $app;

    protected $model;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    abstract protected function getClass(): string;

    protected function makeModel() :void
    {
        $this->model = $this->app->make($this->getClass());
    }
}
