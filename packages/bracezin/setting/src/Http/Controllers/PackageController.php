<?php

namespace Setting\Http\Controllers;

use App\Http\Controllers\MainController;
use Setting\Repositories\SettingRepository;

class PackageController extends MainController
{
    protected $settingRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        SettingRepository $settingRepo,
    ) {
        $this->settingRepository = $settingRepo;
        $this->repository = $this->getRepository();
    }
}
