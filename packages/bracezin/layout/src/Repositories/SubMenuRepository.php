<?php

namespace Layout\Repositories;

use App\Repositories\MainRepository;
use Layout\Models\SubMenu;
use Arr;
use Str;

class SubMenuRepository extends MainRepository {

	public function __construct(SubMenu $subMenu) {
		parent::__construct($subMenu);
	}

	/**
	 * Returns all records.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function all($input = null) {
		$count = SubMenu::get()->count();
		$items = SubMenu::SearchFilter($input)
			->DateFilter($input)
			->IdFilterOn($input, 'menu_id')
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
			->GetData($count, $input);

		return $items;
	}
}
