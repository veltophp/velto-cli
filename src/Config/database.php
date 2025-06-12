<?php


use Velto\Core\Env;

return [
    'driver' => Env::get('DB_CONNECTION', 'mysql'),

    'sqlite' => [
        'database' => BASE_PATH . '/' . Env::get('DB_DATABASE', 'storage/database.sqlite'),
    ],

    'mysql' => [
        'host'     => Env::get('DB_HOST', '127.0.0.1'),
        'database' => Env::get('DB_DATABASE', 'velto'),
        'username' => Env::get('DB_USERNAME', 'root'),
        'password' => Env::get('DB_PASSWORD', ''),
        'charset'  => Env::get('DB_CHARSET', 'utf8mb4'),
    ],
];