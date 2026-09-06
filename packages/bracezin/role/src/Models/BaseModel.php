<?php
namespace Role\Models;

use App\Models\MainModel;
use Role\Traits\PackageTrait;

class BaseModel extends MainModel {
	use PackageTrait;
}
