<?php

use Maatwebsite\Excel\ExcelServiceProvider;
use Maatwebsite\Excel\Facades\Excel;

return [

    'providers' => [
        // Excel Services
        ExcelServiceProvider::class,
    ],

    'aliases' => [
        'Excel' => Excel::class,
    ],
];
