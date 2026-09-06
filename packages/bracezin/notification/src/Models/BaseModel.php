<?php
namespace Notification\Models;

use App\Models\MainModel;
use Notification\Traits\PackageTrait;

class BaseModel extends MainModel {
	use PackageTrait;
}
