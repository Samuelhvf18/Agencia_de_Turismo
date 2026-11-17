<?php

return [
    'temporary_file_upload' => [
        'rules' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:4096'],
        'middleware' => ['auth', 'throttle:20,1'],
    ],
];
