<?php

namespace Notification\Http\Controllers;

use App\Http\Controllers\MainController;
use Notification\Repositories\NotifyRepository;
 

class PackageController extends MainController {
	
	protected $notifyRepository;
	 
	/**
	 * Create a new controller instance.
	 * @return void
	 */
	public function __construct(NotifyRepository $notifyRepo)
	 {
		$this->notifyRepository = $notifyRepo;
		$this->repository = $this->getRepository();
		 
	}
}
