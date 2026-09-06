<?php

namespace Security\Http\Controllers;

use App\Http\Controllers\MainController;
use Security\Repositories\QrRepository;
use Security\Repositories\OtpRepository;
 
class PackageController extends MainController {
	
	protected $qrRepository;
	protected $otpRepository;

	/**
	 * Create a new controller instance.
	 * @return void
	 */
	public function __construct(QrRepository $qrRepo,
								OtpRepository $otpRepo)
	{
		$this->qrRepository = $qrRepo;
		$this->otpRepository = $otpRepo;
		$this->repository = $this->getRepository();
	   
	}
}
