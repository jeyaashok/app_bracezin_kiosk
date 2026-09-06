<?php

namespace Setting\Repositories;

use App\Repositories\MainRepository;
use Illuminate\Database\Eloquent\Collection;
use Setting\Models\Setting;

class SettingRepository extends MainRepository
{
    public function __construct(Setting $setting)
    {
        parent::__construct($setting);
    }

    /**
     * Returns all records.
     *
     * @return Collection|static[]
     */
    public function all($input = null)
    {
        $count = Setting::get()->count();
        $items = Setting::SearchFilter($input)
            ->DateFilter($input)
            ->StringFilterOn($input, 'title')
            ->SlugFilterOn($input)
            ->StringFilterOn($input, 'category')
            ->BooleanFilterOn($input, 'is_editable')
            ->IdFilterOn($input, 'created_by')
            ->IdFilterOn($input, 'updated_by')
            ->OrderByFilter($input)
            ->DeletedFilter($input)
            ->IncludeFilter($input)
            ->GetData($count, $input);

        return $items;
    }

    /**
     * Increment the Record if it value is Number.
     */
    public function incrementValue($slug)
    {
        $setting = $this->findBySlug($slug);
        $input = [
            'value' => ($setting->type_name == 'number') ? $setting->value + 1 : $setting->value,
        ];
        if ($setting && $setting->id) {
            $setting->customUpdate($setting->id, $input);
        }

    }

    /**
     * Get Formated Code with Prefix and Code Values.
     */
    public function getCodeBySlug($prefix, $code)
    {
        $prefixValue = strtoupper($this->findBySlug($prefix)->value);
        $codeValue = $this->findBySlug($code)->value;

        return $prefixValue.'-'.$codeValue;
    }
}
