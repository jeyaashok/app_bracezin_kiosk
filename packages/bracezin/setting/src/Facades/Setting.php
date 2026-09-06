<?php

namespace Setting\Facades;

use Illuminate\Support\Facades\Facade;
use Setting\Helpers\Setting as SettingHelper;

class Setting extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return SettingHelper::class;
    }
}
