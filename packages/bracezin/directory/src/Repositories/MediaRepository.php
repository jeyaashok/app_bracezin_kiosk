<?php

namespace Directory\Repositories;

use App\Repositories\MainRepository;
use Illuminate\Support\Facades\File;
use Directory\Models\Media as MediaModel;
use Media;
use Storage;
use Arr;
use Str;

class MediaRepository extends MainRepository {

    public function __construct(MediaModel $media) {
        parent::__construct($media);
    }

    public function index($input=null) {
        $items = $this->model
            ->DateFilter($input)
            ->NullFilterOn($input)
            ->MorphFilterOn($input, 'resource')
            ->StringFilterOn($input, 'document_type', false)
            ->MorphFilterOn($input, 'model')
            ->IdFilterOn($input, 'user_id')
            ->IdFilterOn($input, 'shared_by')
            ->StringFilterOn($input, 'url', false)
            ->StringFilterOn($input, 'disk')
            ->StringFilterOn($input, 'etag')
            ->StringFilterOn($input, 'extension')
            ->StringFilterOn($input, 'mime')
            ->StringFilterOn($input, 'name')
            ->StringFilterOn($input, 'filename')
            ->StringFilterOn($input, 'type')
            ->BooleanFilterOn($input, 'is_active')
            ->BooleanFilterOn($input, 'is_primary')
            ->BooleanFilterOn($input, 'is_favorite')
            ->BooleanFilterOn($input, 'is_local_server')
            ->IdFilterOn($input, 'created_by')
            ->IdFilterOn($input, 'updated_by')
            ->OrderByFilter($input)
            ->DeletedFilter($input);
        return $items;
    }

    /**
     * Get unused activities for services
     * @param $id
     * @return the record
     */
    public function destroy($id, $isForced = false) {
        $item = $this->model->find($id);
        if($item && $item->id) {
            if($isForced) { $item->forceDelete(); }
            else { $item->delete(); }
        } else {
            throw new \Exception('No Records Found', 405);
        }
        return;
    }

}
