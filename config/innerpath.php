<?php

return [

    'available_locales' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('APP_AVAILABLE_LOCALES', 'ar,en'))
    ))),

    'default_locale' => env('APP_LOCALE', 'ar'),

    'dream_media_disk' => env('DREAM_MEDIA_DISK', 'local'),

    'brand' => [
        'primary' => '#879B88',
        'secondary' => '#E9E1D3',
        'background' => '#F8F6F0',
        'text' => '#303A35',
        'accent' => '#B98570',
        'lavender' => '#B9AECF',
    ],

    'disclaimer' => 'InnerPath is a self-reflection and personal-development platform. It does not diagnose mental disorders, predict the future, or determine psychological state from dream symbols alone.',

];
