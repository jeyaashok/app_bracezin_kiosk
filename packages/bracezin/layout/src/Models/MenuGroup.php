<?php

namespace Layout\Models;

use Layout\Models\BaseModel;
use Sys;

class MenuGroup extends BaseModel {

	protected $table = 'menu_group';

	protected $fillable = array('title', 'slug', 'type', 'translate', 'icon', 'image', 'url', 'route', 'order', 'role', 'permission', 'json', 'is_hidden', 'is_active', 'is_open_new_tab', 'created_by', 'updated_by');

	protected $casts = [
		'is_active' => 'boolean',
		'is_hidden' => 'boolean',
		'is_open_new_tab' => 'boolean',
	];

	protected $appends = ['tableName', 'permissions','roles', 'label', 'isCollapsed'];

	protected $searchable = [
		'columns' => [
			'menu_group.title' => 1,
			'menu_group.translate' => 2,
			'menu_group.url' => 3,
			'menu_group.externalUrl' => 4,
			'menus.title' => 4,
		],
		'joins' => [
			'menus' => ['menu_group.id', 'menus.menu_group_id'],
		],
	];

	public function getPermissionsAttribute() {
		$allPermissions = [];
		$permissions = [];
		if ($this->permission && $this->permission != null) {
			$permissions = explode(",", $this->permission);
			array_push($allPermissions, $permissions);
		}
		$menus = $this->menus();
		$menusLists = $menus->get();
		$menusPermissions = array_merge(...$menus->get('permission')->pluck('permissions')->toArray());
		array_push($allPermissions, $menusPermissions);
		if($menusLists and $menusLists->count() > 0) {
			foreach($menusLists as $menu) {
				$subMenus = $menu->subMenus();
				$subMenusLists = $subMenus->get();
				if($subMenusLists && $subMenusLists->count() > 0) {
					$subMenuPermissions = array_merge(...$subMenus->get('permission')->pluck('permissions')->toArray());
					array_push($allPermissions, $subMenuPermissions);
				}
			}
		}
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
		$menus = $this->menus();
		$menusLists = $menus->get();
		$menusRoles = array_merge(...$menus->get('role')->pluck('roles')->toArray());
		array_push($allRoles, $menusRoles);
		if($menusLists and $menusLists->count() > 0) {
			foreach($menusLists as $menu) {
				$subMenus = $menu->subMenus();
				$subMenusLists = $subMenus->get();
				if($subMenusLists && $subMenusLists->count() > 0) {
					$subMenuRoles = array_merge(...$subMenus->get('role')->pluck('roles')->toArray());
					array_push($allRoles, $subMenuRoles);
				}
			}
		}
		$allRoles = array_values(array_unique(array_merge(...$allRoles)));
		return $this->attributes['roles'] = $allRoles;
	}

	public function getIsCollapsedAttribute() {
		$menus = $this->menus();
		return $this->attributes['isCollapsed'] = ($menus->count() > 0) ? true : false;
	}

}
