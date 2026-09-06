<?php

namespace Role\Http\Controllers;

use App\Http\Controllers\MainController;
use Role\Repositories\PermissionRepository;

class PackageController extends MainController {

	protected $permissionRepository;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(PermissionRepository $permissionRepo,) {
		$this->permissionRepository = $permissionRepo;
		$this->repository = $this->getRepository();
	}
}
