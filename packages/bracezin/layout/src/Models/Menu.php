<?php

namespace Layout\Models;

use Layout\Models\BaseModel;

class Menu extends BaseModel {

	protected $table = 'menu';

	protected $fillable = array('menu_group_id', 'title', 'slug', 'type', 'translate', 'icon', 'image', 'url', 'route', 'order', 'role', 'permission', 'json', 'is_hidden', 'is_active', 'is_open_new_tab', 'created_by', 'updated_by');

	protected $casts = [
		'is_active' => 'boolean',
		'is_hidden' => 'boolean',
		'is_open_new_tab' => 'boolean',
	];

	protected $appends = ['tableName', 'permissions','roles', 'label', 'isCollapsed'];

	protected $searchable = [
		'columns' => [
			'menu.title' => 1,
			'menu.translate' => 2,
			'menu.url' => 3,
			'menu.externalUrl' => 4,
			'menuGroup.title' => 4,
			'subMenus.title' => 4,
		],
		'joins' => [
			'menuGroup' => ['menu_group_id', 'menu_group.id'],
			'subMenus' => ['menu.id', 'subMenus.menu_id'],
		],
	];

	public function getPermissionsAttribute() {
		$allPermissions = [];
		$permissions = [];
		if ($this->permission && $this->permission != null) {
			$permissions = explode(",", $this->permission);
			array_push($allPermissions, $permissions);
		}
		$subMenus = $this->subMenus();
		$subMenusPermissions = array_merge(...$subMenus->get('permission')->pluck('permissions')->toArray());
		array_push($allPermissions, $subMenusPermissions);
		$allPermissions = array_values(array_unique(array_merge(...$allPermissions)));
		return $this->attributes['permissions'] = $allPermissions;
	}

	public function getRolesAttribute() {
		$allRoles = [];
		$roles = [];
		if ($this->role && $this->role != null) {
			$roles = explode(",", str_replace(' ','',$this->role));
			array_push($allRoles, $roles);
		}
		$subMenus = $this->subMenus();
		$subMenusRoles = array_merge(...$subMenus->get('role')->pluck('roles')->toArray());
		array_push($allRoles, $subMenusRoles);
		$allRoles = array_values(array_unique(array_merge(...$allRoles)));
		return $this->attributes['roles'] = $allRoles;
	}

	public function getIsCollapsedAttribute() {
		$subMenus = $this->subMenus();
		return $this->attributes['isCollapsed'] = ($subMenus->count() > 0) ? true : false;
	}

}
