<?php

namespace Security\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Routing\Router;
use Tji\Helpers\MainHelper;
use Carbon\Carbon;
use ErrorResponse;
use Auth;
use Log;

class Qr extends MainHelper {

    public function makeCode($resourceType = null) {
        $timeCode = preg_replace('/[^A-Za-z0-9\-]/', '', Carbon::now()->toIsoString());
        $timeCode = str_replace('-', '', $timeCode);
        $prefix = 'BRZ';
        switch($resourceType) {
            case 'users':
                $prefix = 'USR';
                break;
            default:
                break;
        }
        $code = $prefix.'_'.$timeCode;
        return $code;
    }

    public function generateDiagnoseQrCode($input=[]) {
        $model = @$input['model'];
        $brand = @$input['brand'];
        $serial = @$input['serial'];
        $timeCode = preg_replace('/[^A-Za-z0-9\-]/', '', Carbon::now()->toIsoString());
        $timeCode = str_replace('-', '', $timeCode);
        $prefix = 'DEV';
        if($serial && $brand && $model) {
            $code = $prefix.'_'.$brand.'_'.$model.'_'.$serial;
            $oldQr = $this->qrRepository->findByColumn('code', $code);
            $data = [
                'date' => Carbon::now()->format('Y-M-d h:i:s'),
                'code' => $code,
                'serial' => $serial,
                'brand' => $brand,
                'model' => $model,
                'resource_id' => null,
                'resource_type' => null,
            ];
            // $qrCode = base64_encode(json_encode($data));
            $qrCode = Carbon::now()->format('Y-M-d h:i:s') . $code;
            if($oldQr && $oldQr->id && $oldQr->updated_at >= Carbon::now()->subMinutes(1440)) {
                $oldQrId = $oldQr->id;
                $oldQr->update(['qr_code' => $qrCode]);
                $qr = $this->qrRepository->findById($oldQrId);
            } else {
                $storeData = [
                    'code' => $code,
                    'qr_code' => $qrCode,
                    'resource_id' => null,
                    'resource_type' => null,
                    'is_refreshable' => 0,
                ];
                $qr = $this->qrRepository->store($storeData);
            }
            return ($qr && $qr->id) ? $qr : null;
        }
        return null;
    }

    public function makeQrCode($code, $resourceId, $resourceType) {
        $data = [
            'date' => Carbon::now()->format('Y-M-d h:i:s'),
            'code' => $code,
            'resource_id' => $resourceId,
            'resource_type' => $resourceType,
        ];
        if($code && $resourceId && $resourceType) {
            return base64_encode(json_encode($data));
        }
        return null;
    }

    public function checkAndStoreSingleRefreshableQr($model){
        $qr = null;
        if($model && $model->id) {
            $qr = ($model->qr() && $model->qr()->count() > 0) ? $model->qr()->first() : null;
            if($qr && $qr->id && $qr->is_used == false) {
                if($qr->is_refreshable && $qr->updated_at <= Carbon::now()->subMinutes(2000)) {
                    $qrId =$qr->id;
                    $qrCode =$this->makeQrCode($qr->code, $qr->resource_id, $qr->resource_type);
                    $qr->update(['qr_code' => $qrCode]);
                    $qr = $this->qrRepository->findById($qrId);
                }
            } else {
                $code = $this->makeCode($model->tableName);
                $qrCode =$this->makeQrCode($code, $model->id, $model->tableName);
                $storeData = [
                    'code' => $code,
                    'qr_code' => $qrCode,
                    'resource_id' => $model->id,
                    'resource_type' => $model->tableName,
                    'is_refreshable' => 1,
                ];
                $qr = $this->qrRepository->store($storeData);
            }
        }
        return $qr;
    }

    public function checkAndStoreRecentRefreshableQr($model){
        $qr = null;
        if($model && $model->id) {
            $qr = $model->recentQr();
            if($qr && $qr->id && !$qr->is_used) {
                $qrId =$qr->id;
                // Temporory set Time (oneDay)
                if($qr->is_refreshable && $qr->created_at <= Carbon::now()->subMinutes(1440)) {
                    $qrCode =$this->makeQrCode($qr->code, $qr->resource_id, $qr->resource_type);
                    $qr->update(['qr_code' => $qrCode]);
                }
                $qr = $this->qrRepository->findById($qrId);
            } else {
                $code = $this->makeCode($model->tableName);
                $qrCode =$this->makeQrCode($code, $model->id, $model->tableName);
                $storeData = [
                    'code' => $code,
                    'qr_code' => $qrCode,
                    'resource_id' => $model->id,
                    'resource_type' => $model->tableName,
                    'is_refreshable' => 1,
                ];
                $qr = $this->qrRepository->store($storeData);
            }
        }
        return $qr;
    }

    public function checkAndStorePermenantQr($model){
        $qr = null;
        if($model && $model->id) {
            $qr = $model->recentQr();
            if($qr && $qr->id) {
                $qrId =$qr->id;
                $this->qrRepository->update($qrId, ['is_refreshable' => 0, 'is_used' => 0]);
            } else {
                $code = $this->makeCode($model->tableName);
                $qrCode =$this->makeQrCode($code, $model->id, $model->tableName);
                $storeData = [
                    'code' => $code,
                    'qr_code' => $qrCode,
                    'resource_id' => $model->id,
                    'resource_type' => $model->tableName,
                    'is_refreshable' => 0,
                ];
                $qr = $this->qrRepository->store($storeData);
            }
        }
        return $qr;
    }

    public function readQrcode($input) {
        $qrcode = Arr::has($input, 'qrcode') ? $input['qrcode'] : null;
        if(!$qrcode) { throw new ErrorResponse('Valid Qr Code Required.',405,'info'); }
        if($qrcode) {
            $qr = $this->qrRepository->index(['qr_code' => $qrcode])->first();
            if(!($qr && $qr->id)) {
                throw new ErrorResponse('Invalid Qr or Qr May Expired, Try after sometimes.',405,'info');
            }
            if($qr && $qr->id) {
                $resourceType = $qr->resource_type;
                switch($resourceType) {
                    case 'users':
                        $authUser = auth('admin')->user();
                        if($authUser && $authUser->id) {
                            $resource = $qr->resource;
                            $user = $this->allUserRepository->findById($resource->id);
                            if($user && $user->id && $qr->is_used == false) {
                                $qr->update(['is_used' => true]);
                                return $user;
                            }
                            else {
                                throw new ErrorResponse('Qr Has Been Readed Already.',405,'info');
                            }
                        } else {
                            throw new ErrorResponse('You doesnot have a Permission to Access this facilities.',405,'info');
                        }
                        break;
                    default:
                        break;
                }
            }
        }
    }
}
