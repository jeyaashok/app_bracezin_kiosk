<?php

namespace Database\Seeders;
use Remark\Models\Comment;
use Illuminate\Database\Seeder;
use Excel;


class CommentSeeder extends Seeder {

	protected function getFileName()
	{
		return __DIR__ . '/remark_files/comment.csv';
	}
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		Comment::factory()->count(10)->create();
	}
} 