<?php

namespace Layout\Models;

use Layout\Models\BaseModel;

class SubMenu extends BaseModel {

	protected $table = 'sub_menu';

	protected $fillable = array('menu_id', 'title', 'slug', 'type', 'translate', 'icon', 'image', 'url', 'route', 'order', 'role', 'permission', 'json', 'is_hidden', 'is_active', 'is_open_new_tab', 'created_by', 'updated_by');

	protected $casts = [
		'is_active' => 'boolean',
		'is_hidden' => 'boolean',
		'is_open_new_tab' => 'boolean',
	];

	protected $appends = ['tableName', 'permissions', 'roles', 'label'];

	protected $searchable = [
		'columns' => [
			'sub_menu.title' => 1,
			'sub_menu.translate' => 2,
			'sub_menu.url' => 3,
			'sub_menu.externalUrl' => 4,
			'menu.title' => 4,
		],
		'joins' => [
			'menu' => ['menu_id', 'menu.id'],
		],
	];

	/** Assign the dataType Attribuite. **/
	public function getRolesAttribute() {
		$roles = [];
		if ($this->role && $this->role != null) {
			$roles = explode(",", str_replace(' ','',$this->role));
		}
		return $this->attributes['roles'] = $roles;
	}

}
