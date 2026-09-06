<?php
namespace Setting\Models;

use App\Models\MainModel;
use Setting\Traits\PackageTrait;

class BaseModel extends MainModel {
	use PackageTrait;

	public function getLabelAttribute() {
		return $this->attributes['label'] = $this->title;
	}
}
