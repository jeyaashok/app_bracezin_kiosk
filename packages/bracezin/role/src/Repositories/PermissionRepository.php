<?php

namespace Role\Repositories;

use App\Repositories\MainRepository;
use Role\Models\Permission;
use Arr;
use Str;

class PermissionRepository extends MainRepository {

	public function __construct(Permission $permission) {
		parent::__construct($permission);
	}

	/**
	 * Returns all records.
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function all($input = null) {
		$item = $this->model->get()->all(
		);
		return $item;
	}

	public function findById($id, $input = [] ) {
		return $this->model->find($id);
	}

}
