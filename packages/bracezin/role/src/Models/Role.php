<?php

namespace Role\Models;
use Tji\Traits\SearchableTrait;

class Role extends \Spatie\Permission\Models\Role {

	use SearchableTrait;

	// protected $isCachable = true;
	// protected $cachePrefix = "role";
	// protected $cacheCooldownSeconds = 864000;

	protected $appends = ['tableName'];

	protected $searchable = [
        'columns' => [
			'roles.name' => 1,
        ]
    ];

	// protected $searchable = [
	// 	'columns' => [
	// 		'name' => 1,
	// 	],
	// ];

	/** Assign the dataType Attribuite. **/
	public function getTableNameAttribute() {
		return $this->attributes['tableName'] = $this->getTable();
	}

}
