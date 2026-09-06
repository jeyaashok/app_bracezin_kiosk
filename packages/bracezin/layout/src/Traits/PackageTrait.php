<?php

namespace Layout\Traits;

use Carbon\Carbon;
use Arr;
use Str;

/******** Index *********/
trait PackageTrait {

    /** Get the menuGroup that belongs to this menu. */
    public function menuGroup() {
        return $this->belongsTo('Layout\Models\MenuGroup', 'menu_group_id');
    }

    /** Get the Children from SubMenu Table. */
    public function submenus() {
        return $this->hasMany('Layout\Models\SubMenu')->orderBy('order');
    }

    /** Get the Children from Menu Table. */
    public function menus() {
        return $this->hasMany('Layout\Models\Menu')->orderBy('order');
    }

    /** Get the menuGroup that belongs to this menu. */
    public function menu() {
        return $this->belongsTo('Layout\Models\Menu', 'menu_id');
    }

    /** Get the children that has many items. */
    public function children() {
        $tableName = $this->getTable();
        if($tableName && $tableName === 'menu_group') {
            return $this->hasMany('Layout\Models\Menu')->orderBy('order');
        } elseif($tableName && $tableName === 'menu') {
            return $this->hasMany('Layout\Models\SubMenu')->orderBy('order');
        } else {
            return null;
        }
    }

}
