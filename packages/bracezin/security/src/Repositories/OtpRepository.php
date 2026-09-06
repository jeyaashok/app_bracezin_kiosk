<?php

namespace Security\Repositories;

use App\Repositories\MainRepository;
use Security\Models\Otp;
use Arr;
use Str;

class OtpRepository extends MainRepository {

	public function __construct(Otp $otp) {
		parent::__construct($otp);
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
            ->MorphFilterOn($input, 'resource')
            ->IdFilterOn($input, 'created_by')
            ->IdFilterOn($input, 'updated_by')
            ->OrderByFilter($input)
            ->DeletedFilter($input)
            ->IncludeFilter($input);

		return $items;
	}

}
