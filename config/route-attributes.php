<?php

return [
    'enabled' => true,
    'directories' => [
        app_path('Http/Controllers/Api') => [
            'prefix' => 'api',
            'middleware' => 'api',
        ],
        app_path('Http/Controllers/Web') => [
            'prefix' => null,
            'middleware' => 'web'
        ],
    ],
    'middleware' => [
        \Illuminate\Routing\Middleware\SubstituteBindings::class
    ],
    'scope-bindings' => null,
];