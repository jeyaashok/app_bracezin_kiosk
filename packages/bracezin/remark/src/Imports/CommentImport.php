<?php

namespace Remark\Imports;

use Remark\Models\Comment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Arr;
use Str;
// use Faker\Generator;

class CommentImport implements ToCollection, WithHeadingRow {
	public function collection(Collection $comments) {
		foreach ($comments as $comment) {
			$data = array(
				'id' => trim($comment['id']),
				'resource_id' => trim($comment['resource_id']),
				'resource_type' => trim($comment['resource_type']),
				'comment_id' => trim($comment['comment_id']),
				'user_id' => trim($comment[' user_id']),
				'title' => trim($comment['title']),
				'description' => trim($comment['description']),
				'rating'=> trim($comment['rating']),
			);
			$comment = Comment::create($data);
		}
	}
}
