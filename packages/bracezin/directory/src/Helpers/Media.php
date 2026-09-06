<?php

namespace Directory\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Routing\Router;
use Directory\Repositories\MediaRepository;
use Tji\Helpers\MainHelper;
use Log;
use Session;
use Auth;
use UserFcd;
use Sys;

class Media extends MainHelper {

    public function getTypeByFileType($fileType = null) {
        if(!$fileType) { return 'document'; }
        $output = 'document';
        switch ($fileType) {
            case 'image/png':
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/bmp':
            case 'image/gif':
            case 'image/svg+xml':
            case 'image/tiff':
            case 'image/webp':
            case 'image/ico':
            case 'image':
                $output = 'image';
                break;
            case 'audio/aac':
            case 'audio/x-aac':
            case 'audio/mpeg':
            case 'audio/ogg':
            case 'audio/opus':
            case 'audio/wav':
            case 'audio/webm':
            case 'audio/3gpp':
            case 'audio/3gpp2':
            case 'audio':
            case 'voice':
                $output = 'voice';
                break;
            case 'video/x-msvideo':
            case 'video/mp4':
            case 'video/mov':
            case 'video/quicktime':
            case 'video/mpeg':
            case 'video/ogg':
            case 'video/webm':
            case 'video/3gpp':
            case 'video/3gpp2':
            case 'video':
                $output = 'video';
                break;
            default :
                $output = 'document';
                break;
        }
        return $output;
    }

    public function storeMedia($input) {
        return $this->mediaRepository->store($input);
    }

    public function updateMedia($id, $input) {
        return $this->mediaRepository->update($id, $input);
    }

    public function deleteImage($id) {
        return $this->mediaRepository->delete($id, true);
    }

    public function getExistingMediaByResource($input, $isPrimary = true) {
        $resourceId = @$input['resource_id'] ?: null;
        $resourceType = @$input['resource_type'] ?: null;
        $documentType = @$input['document_type'] ?: null;
        $input['is_primary'] = @$input['is_primary'] ?: $isPrimary ?: 0;
        if(!($resourceId && $resourceType)) { return null; }
        $media = $this->mediaRepository->index($input)->latest()->first();
        return ($media && $media->id) ? $media : null;
    }

}
