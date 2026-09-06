<?php

namespace Directory\Http\Controllers;

use App\Http\Controllers\MainController;
use Directory\Repositories\MediaRepository;

class PackageController extends MainController {

    public $mediaRepository;

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct(MediaRepository $mediaRepo)
    {
        $this->mediaRepository = $mediaRepo;
        $this->repository = $this->getRepository();
    }
}
