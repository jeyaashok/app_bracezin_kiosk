<?php

namespace Notification\Repositories;

use App\Repositories\MainRepository;
use Notification\Models\Notify;
use Arr;
use Str;

class NotifyRepository extends MainRepository {

	public function __construct(Notify $notify) {
		parent::__construct($notify);
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
            ->IdFilterOn($input,'user_id')
            ->StringFilterOn($input, 'type')
            ->StringFilterOn($input, 'title')
            ->StringFilterOn($input, 'description')
            ->StringFilterOn($input, 'icon')
            ->StringFilterOn($input, 'color')              
			->BooleanFilterOn($input, 'is_notified')
			->BooleanFilterOn($input, 'is_read')
			->BooleanFilterOn($input, 'is_web_notify')
			->BooleanFilterOn($input, 'is_desktop_notify')
			->BooleanFilterOn($input, 'is_sound_notify')		 
            ->IdFilterOn($input, 'created_by')
            ->IdFilterOn($input, 'updated_by')
            ->OrderByFilter($input)
            ->DeletedFilter($input)
            ->IncludeFilter($input);

		return $items;
	}

}
