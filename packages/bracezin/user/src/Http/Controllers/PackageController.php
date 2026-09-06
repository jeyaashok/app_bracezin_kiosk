<?php

namespace User\Http\Controllers;

use App\Http\Controllers\MainController;
use User\Repositories\AllUserRepository;
use User\Repositories\UserRepository;
use User\Repositories\UserDetailRepository;

class PackageController extends MainController {

	protected $allUserRepository;
	protected $userDetailRepository;
	protected $userRepository;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(UserDetailRepository $userDetailRepo,
								AllUserRepository $allUserRepo,
								UserRepository $userRepo) {
		$this->userDetailRepository = $userDetailRepo;
		$this->allUserRepository = $allUserRepo;
		$this->userRepository = $userRepo;
		$this->repository = $this->getRepository();
	}
}
