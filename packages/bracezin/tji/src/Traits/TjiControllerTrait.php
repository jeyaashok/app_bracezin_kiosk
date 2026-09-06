<?php

namespace Tji\Traits;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Storage;
use Tji;
use Arr;
use Str;
use Log;
use DB;

trait TjiControllerTrait {
	
    public function enableQueryLog() {
        return env('ENABLE_QUERYLOG', false);
    }

	/**
     * Display a listing of the records. (Get Method)
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) {
        if($this->enableQueryLog()) { DB::connection()->enableQueryLog(); }
        $input = $request->all();
        $input = $this->indexDataInit($input);
        $items = ($this->repository) ? $this->repository->all($input) : null;
        $items = $this->indexResponseInit($items, $input);
        if($this->enableQueryLog()) { Log::alert(DB::getQueryLog()); }
        return Tji::indexResponse($request, ['data' => $items]);
    }

    /**
     * Store a newly created Record in storage. (Post Method)
     * @param $request
     * @return Response
     */
    public function store(Request $request) {
        DB::beginTransaction();
        try {
            if($this->enableQueryLog()) { DB::connection()->enableQueryLog(); }
            $input = $request->all();
            $input = $this->storeDataInit($input);
            $item = ($this->repository) ? $this->repository->store($input) : null;
            $item = $this->storeResponseInit($item, $input);
            DB::commit();
            if($this->enableQueryLog()) { Log::alert(DB::getQueryLog()); }
            return Tji::storeResponse($request, ['data' => $item]);
        } catch(\Exception $e) {
            DB::rollBack();
            return Tji::errorResponse($request, $e);
        }
    }

    /**
     * Update the specified record in storage. (Put Method)
     * @param int        $id
     * @param $request
     * @return Response
     */
    public function update(Request $request, $id) {
        DB::beginTransaction();
        try {
            if($this->enableQueryLog()) { DB::connection()->enableQueryLog(); }
            $input = $request->all();
            $input = $this->updateDataInit($id, $input);
            $item = ($this->repository) ? $this->repository->update($id, $input) : null;
            $item = $this->updateResponseInit($item, $input);
            DB::commit();
            if($this->enableQueryLog()) { Log::alert(DB::getQueryLog()); }
            return Tji::updateResponse($request, ['data' => $item]);
        } catch(\Exception $e) {
            DB::rollBack();
            return Tji::errorResponse($request, $e);
        }
    }

    /**
     * Get a single record with the id (Get Method)
     * @param  int  $id
     * @return Response
     */
    public function show(Request $request, $id) {
        if($this->enableQueryLog()) { DB::connection()->enableQueryLog(); }
        $input = $request->all();
        $item = ($this->repository) ? $this->repository->findById((int)$id, $input) : null;
        $item = $this->showResponseInit($item, $input);
        if($this->enableQueryLog()) { Log::alert(DB::getQueryLog()); }
        return Tji::showResponse($request, ['data' => $item]);
    }

    /**
     * Remove the specified category from storage. (Delete Method)
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request, $id) {
        DB::beginTransaction();
        try {
            if($this->enableQueryLog()) { DB::connection()->enableQueryLog(); }
            $input = $request->all();
            $input = $this->destroyDataInit($id, $input);
            $item = ($this->repository) ? $this->repository->destroy((int)$id) : null;
            $item = $this->destroyResponseInit($item, $input);
            DB::commit();
            if($this->enableQueryLog()) { Log::alert(DB::getQueryLog()); }
            return Tji::deleteResponse($request, ['message' => 'Deleted Successfully.']);
        } catch(\Exception $e) {
            DB::rollBack();
            return Tji::errorResponse($request, $e);
        }
    }

    /**
     * Remove the Multiple records from storage. (Post Method)
     * @param Array [$id] or comma seperated id string(#,#,...)
     * @return Response
     */
    public function multiDestroy(Request $request) {
        DB::beginTransaction();
        try {
            if($this->enableQueryLog()) { DB::connection()->enableQueryLog(); }
            $input = $request->all();
            $ids = Arr::has($input, 'ids') ? $input['ids'] : null;
            $replaces = ['[', ']', '-', ' ', '{', '}', '(', ')', '&#39;'];
            $ids = ($ids && !is_array($ids)) ? explode(',', str_replace($replaces, '', $ids)) : $ids;
            $idsArray = ($ids && is_array($ids)) ? $ids : [];
            $item = ($this->repository) ? $this->repository->multiDestroy($idsArray) : null;
            DB::commit();
            if($this->enableQueryLog()) { Log::alert(DB::getQueryLog()); }
            return Tji::deleteResponse($request, ['message' => 'Deleted all Selected Records Successfully.']);
        } catch(\Exception $e) {
            DB::rollBack();
            return Tji::errorResponse($request, $e);
        }
    }

    /**
     * Store Media on Existing Record in DB. (Post Method)
     * @param $request
     * @return Response
     */
    public function storeMedia(Request $request) {
        DB::beginTransaction();
        try {
            if($this->enableQueryLog()) { DB::connection()->enableQueryLog(); }
            $input = $request->all();
            $id = Arr::has($input, 'id') ? $input['id'] : null;
            $id = (is_null($id) && Arr::has($input, 'resource_id')) ? $input['resource_id'] : $id;
            $item = $this->repository->findById($id);
            if($item && $item->id) {
                $input['id'] = $id;
                $media = $item->storeMedia($input);
                $item = $this->repository->findById($id);
            }
            DB::commit();
            if($this->enableQueryLog()) { Log::alert(DB::getQueryLog()); }
            return Tji::updateResponse($request, ['data' => $item]);
        } catch(\Exception $e) {
            DB::rollBack();
            return Tji::errorResponse($request, $e);
        }
    }

    // ***
    // Before collections Process
    // ***
    public function indexDataInit($input) {
        return $input;
    }

    public function storeDataInit($input) {
        return $input;
    }

    public function updateDataInit($id, $input) {
        return $input;
    }

    public function showDataInit($id, $input) {
        return $input;
    }

    public function destroyDataInit($id, $input) {
        return $input;
    }


    // ***
    // After collections Process
    // ***
    public function indexResponseInit($items, $input) {
        return $items;
    }

    public function storeResponseInit($item, $input) {
        return $item;
    }

    public function updateResponseInit($item, $input) {
        return $item;
    }

    public function showResponseInit($item, $input) {
        return $item;
    }

    public function destroyResponseInit($item, $input) {
        return $item;
    }

}
