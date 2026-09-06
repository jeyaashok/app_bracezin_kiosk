<?php

namespace Role\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Role\Models\Permission;

class PermissionImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $permissions)
    {
        $hasModuleColumn = Schema::hasColumn('permissions', 'module');

        foreach ($permissions as $permission) {
            $data = [
                'name' => trim($permission['name']),
                'guard_name' => trim($permission['guard'] ?? 'admin'),
            ];

            if ($hasModuleColumn) {
                $data['module'] = trim($permission['module'] ?? '');
            }

            Permission::firstOrCreate(
                [
                    'name' => $data['name'],
                    'guard_name' => $data['guard_name'],
                ],
                $hasModuleColumn ? ['module' => $data['module']] : []
            );
        }
    }
}
