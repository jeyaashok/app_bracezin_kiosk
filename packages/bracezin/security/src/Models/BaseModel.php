<?php
namespace Security\Models;

use App\Models\MainModel;
use Security\Traits\PackageTrait;

class BaseModel extends MainModel {
	use PackageTrait;
}
