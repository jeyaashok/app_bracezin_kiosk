<?php

namespace Layout\Repositories;

use App\Repositories\MainRepository;
use Layout\Models\Menu;
use Arr;
use Str;

class MenuRepository extends MainRepository {

	public function __construct(Menu $menu) {
		parent::__construct($menu);
	}

	/**
	 * Returns all records.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function all($input = null) {
		$count = Menu::get()->count();
		$items = Menu::SearchFilter($input)
			->DateFilter($input)
			->IdFilterOn($input, 'menu_group_id')
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
			->with('children')
			->GetData($count, $input);

		return $items;
	}

}
