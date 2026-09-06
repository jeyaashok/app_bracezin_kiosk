<?php

namespace App\Repositories;

use Tji\Traits\TjiRepositoryTrait;

class MainRepository
{
    use TjiRepositoryTrait;

    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }
}
