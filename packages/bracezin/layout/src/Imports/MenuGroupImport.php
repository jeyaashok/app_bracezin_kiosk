<?php

namespace Layout\Imports;

use Layout\Models\MenuGroup;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Arr;
use Str;

class MenuGroupImport implements ToCollection, WithHeadingRow {
	public function collection(Collection $menuGroups) {
		$count = MenuGroup::all()->count();
		foreach ($menuGroups as $group) {
			$count = ($count && (int) $count > 0) ? (int) $count + 1 : 1;
			$slug = (string) $count . (string) Str::slug(trim($group['title']), '-');
			$data = array(
				'title' => trim($group['title']),
				'slug' => trim($group['slug']),
				'type' => trim($group['type']),
				'translate' => trim($group['translate']),
				'icon' => trim($group['icon']),
				'image' => trim($group['image']),
				'url' => trim($group['url']),
				'route' => trim($group['route']),
				'order' => trim($group['order']),
				'role' => trim($group['role']),
				'permission' => trim($group['permission']),
				'json' => trim($group['json']),
				'is_hidden' => trim($group['is_hidden']),
				'is_open_new_tab' => trim($group['is_open_new_tab']),
				'is_active' => trim($group['is_active']),
			);
			MenuGroup::create($data);
		}
	}
}
