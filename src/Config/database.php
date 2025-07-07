<?php


use Velto\Core\Env\Env;

return [
    'driver' => Env::get('DB_CONNECTION'),

    'sqlite' => [
        'database' => BASE_PATH . '/' . Env::get('DB_DATABASE'),
    ],

    'mysql' => [
        'host'     => Env::get('DB_HOST'),
        'database' => Env::get('DB_DATABASE'),
        'username' => Env::get('DB_USERNAME'),
        'password' => Env::get('DB_PASSWORD'),
        'charset'  => Env::get('DB_CHARSET', 'utf8mb4'),
    ],
];