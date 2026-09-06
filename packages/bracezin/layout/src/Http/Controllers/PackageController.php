<?php

namespace Layout\Http\Controllers;

use App\Http\Controllers\MainController;
use Layout\Repositories\MenuRepository;
use Layout\Repositories\MenuGroupRepository;
use Layout\Repositories\SubMenuRepository;

class PackageController extends MainController {
	
	protected $menuRepository;
	protected $menuGroupRepository;
	protected $subMenuRepository;
	
	/**
	 * Create a new controller instance.
	 * @return void
	 */
	public function __construct(MenuRepository $menuRepo,
		MenuGroupRepository $menuGroupRepo,
		SubMenuRepository $subMenuRepo) {
		$this->menuRepository = $menuRepo;
		$this->menuGroupRepository = $menuGroupRepo;
		$this->subMenuRepository = $subMenuRepo;
		$this->repository = $this->getRepository();
	}
}
