<?php

namespace Layout\Repositories;

use App\Repositories\MainRepository;
use Layout\Models\MenuGroup;
use Arr;
use Str;

class MenuGroupRepository extends MainRepository {

	public function __construct(MenuGroup $menuGroup) {
		parent::__construct($menuGroup);
	}

	/**
	 * Returns all records.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function all($input = null) {
		$count = MenuGroup::get()->count();
		$items = MenuGroup::SearchFilter($input)
			->DateFilter($input)
			->StringFilterOn($input, 'title')
			->SlugFilterOn($input)
			->BooleanFilterOn($input, 'is_hidden')
			->BooleanFilterOn($input, 'is_exactMatch')
			->BooleanFilterOn($input, 'is_open_new_tab')
			->BooleanFilterOn($input, 'is_active')
			->IdFilterOn($input, 'created_by')
			->IdFilterOn($input, 'updated_by')
			->OrderByFilter($input)
			->DeletedFilter($input)
			->IncludeFilter($input)
			->with('children', 'children.children')
			->GetData($count, $input);

		return $items;
	}

}
