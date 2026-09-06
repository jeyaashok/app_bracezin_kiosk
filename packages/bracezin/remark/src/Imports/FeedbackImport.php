<?php

namespace Remark\Imports;

use Remark\Models\feedback;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Arr;
use Str;
// use Faker\Generator;

class FeedbackImport implements ToCollection, WithHeadingRow {
	public function collection(Collection $feedback) {
		foreach ($feedbacks as $feedback) {
			$data = array(
				'id' => trim($feedback['id']),
				'resource_id' => trim($feedback['resource_id']),
				'resource_type' => trim($feedback['resource_type']),
				'feedback_id' => trim($feedback['feedback_id']),
				'user_id' => trim($feedback['user_id']),
				'title' => trim($feedback['title']),
				'description' => trim($feedback['description']),
				'rating' => trim($feedback['rating']),
 			);
			$feedback = Feedback::create($data);
		}
	}
}