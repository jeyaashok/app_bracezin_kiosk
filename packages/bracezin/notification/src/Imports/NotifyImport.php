<?php

namespace Notification\Imports;

use Notification\Models\Notify;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Arr;
use Str;

// use Faker\Generator;

class NotifyImport implements ToCollection, WithHeadingRow {
	public function collection(Collection $notifys) {
		foreach ($notifys as $notify) {
			$data = array(
				'id' => trim($notify['id']),
				'user_id' => trim($notify['user_id']),
				'type' => trim($notify['type']),
				'title' => trim($notify['title']),
				'description' => trim($notify['description']),
				'icon' => trim($notify['icon']),
				'color' => trim($notify['color']),
				'is_notified'=> trim($notify['is_notified']),
				'is_read'=> trim($notify['is_read']),
				'is_sound_notify'=> trim($notify['is_sound_notify']),
				'is_web_notify'=> trim($notify['is_web_notify']),
				'is_desktop_notify'=> trim($notify['is_desktop_notify']),
			 
			);
			$notify = Notify::create($data);
		}
	}

}
