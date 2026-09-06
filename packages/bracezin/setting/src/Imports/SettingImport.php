<?php

namespace Setting\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Setting\Models\Setting;
use Str;

class SettingImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $settings)
    {
        foreach ($settings as $setting) {
            $data = [
                'title' => trim($setting['title']),
                'slug' => Str::slug(trim($setting['title']), '-'),
                'value' => trim($setting['value']),
                'type' => $this->gettitleType(trim($setting['type'])),
                'category' => trim($setting['category']),
                'description' => trim($setting['description']),
            ];
            Setting::create($data);
        }
    }

    public function gettitleType($type)
    {
        switch ($type) {
            case 'text':
                return 1;
            case 'number':
                return 2;
            case 'email':
                return 3;
            case 'email.multiple':
                return 4;
            case 'address':
                return 5;
            case 'link':
                return 6;
            case 'setting':
                return 7;
            case 'boolean':
                return 8;
            default:
                return null;
        }
    }
}
