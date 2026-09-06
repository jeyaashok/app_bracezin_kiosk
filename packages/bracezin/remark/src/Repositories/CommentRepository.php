<?php

namespace Remark\Repositories;

use App\Repositories\MainRepository;
use Remark\Models\Comment;
use Arr;
use Str;

class CommentRepository extends MainRepository {

	public function __construct(Comment $comment) {
		parent::__construct($comment);
	}

	/**
	 * Returns Index all records.
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function index($input = null) {
		$count = 100;
		$items = $this->model->SearchFilter($input)
            ->DateFilter($input)
            ->NullFilterOn($input)
            ->IdFilterOn($input, 'id')
            ->IdFilterOn($input, 'resource_id')
            ->IdFilterOn($input, 'comment_id')
            ->IdFilterOn($input, 'user_id')
            ->IdFilterOn($input, 'rating')
            ->StringFilterOn($input, 'resource_type')
            ->StringFilterOn($input, 'title')
            ->StringFilterOn($input, 'description') 
            ->IdFilterOn($input, 'created_by')
            ->IdFilterOn($input, 'updated_by')
            ->OrderByFilter($input)
            ->DeletedFilter($input)
            ->IncludeFilter($input);
		return $items;
	}

}
