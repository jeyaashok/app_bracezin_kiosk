<?php

namespace Remark\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Remark\Http\Controllers\PackageController;
use Carbon\Carbon;
use Storage;
use Tji;
use Arr;
use Str;
use DB; 

class FeedbackController extends PackageController {

    public function getRepository() {
        return $this->feedbackRepository;
    }
}

     