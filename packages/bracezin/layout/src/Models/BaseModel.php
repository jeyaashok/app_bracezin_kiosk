<?php
namespace Layout\Models;

use App\Models\MainModel;
use Layout\Traits\PackageTrait;

class BaseModel extends MainModel {
	use PackageTrait;

	public function getLabelAttribute() {
		return $this->attributes['label'] = $this->title;
	}

	/** Assign the dataType Attribuite. **/
	public function getPermissionsAttribute() {
		$permissions = [];
		if ($this->permission && $this->permission != null) {
			$permissions = explode(",", $this->permission);
		}
		return $this->attributes['permissions'] = $permissions;
	}

	/** Assign the dataType Attribuite. **/
	public function getRolesAttribute() {
		$roles = [];
		if ($this->role && $this->role != null) {
			$roles = explode(",", str_replace(' ','',$this->role));
		}
		return $this->attributes['roles'] = $roles;
	}
}
