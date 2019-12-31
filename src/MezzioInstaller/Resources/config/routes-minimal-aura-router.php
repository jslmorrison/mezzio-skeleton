<?php

return [
    'dependencies' => [
        'invokables' => [
            Mezzio\Router\RouterInterface::class => Mezzio\Router\AuraRouter::class,
        ],
        // Map middleware -> factories here
        'factories' => [
        ],
    ],

    'routes' => [
        // Example:
        // [
        //     'name' => 'home',
        //     'path' => '/',
        //     'middleware' => App\Action\HomePageAction::class,
        //     'allowed_methods' => ['GET'],
        // ],
    ],
];