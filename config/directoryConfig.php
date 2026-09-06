<?php

return [
	/*
     * The class name of the media model that holds all medias.
     *
     * The model must be or extend `Directory\Media`.
     */
	'media_model' => Directory\Models\Media::class,
	
    /*
     * The resource_id of the column which holds the ID of the model related to the media.
     *
     * You can change this value if you have set a different name in the migration for the medias table.
     */
	'model_primary_key_attribute' => 'resource_id',

	'base_url' => env('BASE_URL', 'http://localhost:8000'),
	'default_image' => '/api/directory/media/6',
	'logo_bg' => '/api/directory/media/6',
	'favicon' => '/api/directory/media/4',
	'avatar' => '/api/directory/media/2',
    'AWS_URL' => 'https://citymobiletech.s3.ap-south-1.amazonaws.com',

    'append_proofs'=> [
        'partner'=> [
            'proof:id_front', 'proof:id_back', 'proof:address_front', 'proof:address_back'
        ],
        'diagnose'=> [
            'proof:mobile_front', 'proof:mobile_back', 'proof:signature'
        ],
    ],
];
