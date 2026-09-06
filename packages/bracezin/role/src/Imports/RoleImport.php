<?php

namespace Role\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Role\Models\Role;

class RoleImport implements ToCollection, WithHeadingRow {
	public function collection(Collection $roles) {
		foreach ($roles as $role) {
			$data = array(
				'name' => trim($role['name']),
				'guard_name' => trim($role['guard'] ?? 'admin'),
			);
			Role::firstOrCreate($data);
		}
	}

}
