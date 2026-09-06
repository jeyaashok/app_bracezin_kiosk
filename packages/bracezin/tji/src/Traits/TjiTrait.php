<?php

namespace Tji\Traits;

use Directory\Traits\DirectoryTrait;
use Layout\Traits\LayoutTrait;
use Notification\Traits\NotificationTrait;
use Role\Traits\RoleTrait;
use Security\Traits\SecurityTrait;
use Setting\Traits\SettingTrait;
use User\Traits\UserTrait;

trait TjiTrait
{
    use DirectoryTrait, LayoutTrait, NotificationTrait, RoleTrait, SearchableTrait, SecurityTrait, SettingTrait, TjiModelTrait, UserTrait;
}
