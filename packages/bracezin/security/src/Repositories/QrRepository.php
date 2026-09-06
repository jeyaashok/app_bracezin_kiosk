<?php

namespace Security\Repositories;

use App\Repositories\MainRepository;
use Security\Models\Qr;
use Arr;
use Str;

class QrRepository extends MainRepository {

	public function __construct(Qr $qr) {
		parent::__construct($qr);
	}

	/**
	 * Returns Index all records.
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function index($input = null) {
		$count = 100;
		$items = $this->model->SearchFilter($input)
            ->DateFilter($input)
            ->NullFilterOn($input)
            ->IdFilterOn($input,'id')
            ->StringFilterOn($input, 'code')
            ->StringFilterOn($input, 'qr_code', false)
            ->MorphFilterOn($input, 'resource')
			->BooleanFilterOn($input, 'is_refreshable')
            ->BooleanFilterOn($input, 'is_used')
            ->IdFilterOn($input, 'created_by')
            ->IdFilterOn($input, 'updated_by')
            ->OrderByFilter($input)
            ->DeletedFilter($input)
            ->IncludeFilter($input);

		return $items;
	}

}
