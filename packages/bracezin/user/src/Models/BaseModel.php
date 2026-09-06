<?php
namespace User\Models;

use App\Models\MainModel;
use User\Traits\PackageTrait;

class BaseModel extends MainModel {
	use PackageTrait;
}
