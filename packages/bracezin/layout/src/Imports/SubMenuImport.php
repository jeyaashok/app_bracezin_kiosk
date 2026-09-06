<?php

namespace Layout\Imports;

use Layout\Models\MenuGroup;
use Layout\Models\Menu;
use Layout\Models\SubMenu;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Arr;
use Str;

class SubMenuImport implements ToCollection, WithHeadingRow {
	public function collection(Collection $subMenus) {
		// Get Menu ID
		$menusData = Menu::all();
		$menus = array();
		foreach ($menusData as $menu) {
			$menus[$menu['id']] = $menu['slug'];
		}

		$count = SubMenu::all()->count();
		foreach ($subMenus as $subMenu) {
			$count = ($count && (int) $count > 0) ? (int) $count + 1 : 1;
			$slug = (string) $count . (string) Str::slug(trim($subMenu['title']), '-');
			$data = array(
				'menu_id' => array_search(trim($subMenu['menu']), $menus),
				'title' => trim($subMenu['title']),
				'slug' => trim($subMenu['slug']),
				'type' => trim($subMenu['type']),
				'translate' => trim($subMenu['translate']),
				'icon' => trim($subMenu['icon']),
				'image' => trim($subMenu['image']),
				'url' => trim($subMenu['url']),
				'route' => trim($subMenu['route']),
				'order' => trim($subMenu['order']),
				'role' => trim($subMenu['role']),
				'permission' => trim($subMenu['permission']),
				'json' => trim($subMenu['json']),
				'is_hidden' => trim($subMenu['is_hidden']),
				'is_open_new_tab' => trim($subMenu['is_open_new_tab']),
				'is_active' => trim($subMenu['is_active']),
			);
			SubMenu::create($data);
		}
	}
}
