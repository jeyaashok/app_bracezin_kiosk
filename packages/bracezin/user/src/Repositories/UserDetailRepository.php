<?php

namespace User\Repositories;

use App\Repositories\MainRepository;
use Illuminate\Database\Eloquent\Collection;
use User\Models\UserDetail;

class UserDetailRepository extends MainRepository
{
    public function __construct(UserDetail $userDetail)
    {
        parent::__construct($userDetail);
    }

    /**
     * Returns Index all records.
     *
     * @return Collection|static[]
     */
    public function index($input = null)
    {
        $count = 100;
        $items = $this->model
            ->DateFilter($input)
            ->NullFilterOn($input)
            ->IdFilterOn($input, 'id')
            ->IdFilterOn($input, 'user_id')
            ->IdFilterOn($input, 'created_by')
            ->IdFilterOn($input, 'updated_by')
            ->OrderByFilter($input)
            ->DeletedFilter($input)
            ->IncludeFilter($input);

        return $items;
    }
}
