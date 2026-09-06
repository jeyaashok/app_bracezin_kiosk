<?php

namespace Remark\Http\Controllers;

use App\Http\Controllers\MainController;
use Remark\Repositories\CommentRepository;
use Remark\Repositories\FeedbackRepository;

class PackageController extends MainController {
	
	protected $commentRepository;
	protected $feedbackRepository;
	
	/**
	 * Create a new controller instance.
	 * @return void
	 */
	public function __construct(CommentRepository $commentRepo,
		FeedbackRepository $feedbackRepo) 
	{
		$this->commentRepository = $commentRepo;
		$this->feedbackRepository = $feedbackRepo;
		$this->repository = $this->getRepository();
	}
}
