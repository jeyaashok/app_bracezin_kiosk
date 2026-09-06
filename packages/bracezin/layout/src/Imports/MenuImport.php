<?php

namespace Layout\Imports;

use Layout\Models\MenuGroup;
use Layout\Models\Menu;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Arr;
use Str;

class MenuImport implements ToCollection, WithHeadingRow {
	public function collection(Collection $menus) {
		// Get Menu Group ID
		$menuGroupsData = MenuGroup::all();
		$menuGroups = array();
		foreach ($menuGroupsData as $group) {
			$menuGroups[$group['id']] = $group['slug'];
		}

		$count = Menu::all()->count();
		foreach ($menus as $menu) {
			$count = ($count && (int) $count > 0) ? (int) $count + 1 : 1;
			$slug = (string) $count . (string) Str::slug(trim($menu['title']), '-');
			$data = array(
				'menu_group_id' => array_search(trim($menu['menugroup']), $menuGroups),
				'title' => trim($menu['title']),
				'slug' => trim($menu['slug']),
				'type' => trim($menu['type']),
				'translate' => trim($menu['translate']),
				'icon' => trim($menu['icon']),
				'image' => trim($menu['image']),
				'url' => trim($menu['url']),
				'route' => trim($menu['route']),
				'order' => trim($menu['order']),
				'role' => trim($group['role']),
				'permission' => trim($menu['permission']),
				'json' => trim($menu['json']),
				'is_hidden' => trim($menu['is_hidden']),
				'is_open_new_tab' => trim($menu['is_open_new_tab']),
				'is_active' => trim($menu['is_active']),
			);
			Menu::create($data);
		}
	}
}
