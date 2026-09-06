<?php

return [

	'providers' => [
		//Excel Services
		Maatwebsite\Excel\ExcelServiceProvider::class,
	],

	'aliases' => [
		'Excel' => Maatwebsite\Excel\Facades\Excel::class,
	],
];
