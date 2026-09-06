<?php
namespace Remark\Models;

use App\Models\MainModel;
use Remark\Traits\PackageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BaseModel extends MainModel {
	use HasFactory, PackageTrait;
}
