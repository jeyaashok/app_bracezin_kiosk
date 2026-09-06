<?php

namespace Tji\Helpers;

use Directory\Repositories\MediaRepository;
use Layout\Repositories\MenuGroupRepository;
use Layout\Repositories\MenuRepository;
use Layout\Repositories\SubMenuRepository;
use Notification\Repositories\NotifyRepository;
use Role\Repositories\PermissionRepository;
use Role\Repositories\RoleRepository;
use Security\Repositories\OtpRepository;
use Security\Repositories\QrRepository;
use Setting\Repositories\SettingRepository;
use User\Repositories\AllUserRepository;
use User\Repositories\UserDetailRepository;
use User\Repositories\UserRepository;

class MainHelper
{
    protected $repository;

    protected $mediaRepository;

    protected $menuRepository;

    protected $menuGroupRepository;

    protected $subMenuRepository;

    protected $notifyRepository;

    protected $permissionRepository;

    protected $roleRepository;

    protected $otpRepository;

    protected $qrRepository;

    protected $settingRepository;

    protected $userDetailRepository;

    protected $allUserRepository;

    protected $userRepository;

    public function __construct(
        MediaRepository $mediaRepo,
        MenuRepository $menuRepo,
        MenuGroupRepository $menuGroupRepo,
        SubMenuRepository $subMenuRepo,
        NotifyRepository $notifyRepo,
        PermissionRepository $permissionRepo,
        RoleRepository $roleRepo,
        OtpRepository $otpRepo,
        QrRepository $qrRepo,
        SettingRepository $settingRepo,
        UserDetailRepository $userDetailRepo,
        AllUserRepository $allUserRepo,
        UserRepository $userRepo
    ) {
        $this->mediaRepository = $mediaRepo;

        $this->menuRepository = $menuRepo;
        $this->menuGroupRepository = $menuGroupRepo;
        $this->subMenuRepository = $subMenuRepo;

        $this->notifyRepository = $notifyRepo;

        $this->permissionRepository = $permissionRepo;
        $this->roleRepository = $roleRepo;

        $this->otpRepository = $otpRepo;
        $this->qrRepository = $qrRepo;

        $this->settingRepository = $settingRepo;

        $this->userDetailRepository = $userDetailRepo;
        $this->allUserRepository = $allUserRepo;
        $this->userRepository = $userRepo;
    }
}
