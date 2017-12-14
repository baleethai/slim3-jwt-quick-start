<?php

/**
 * Main application development configuration
 */
return [
    'displayErrorDetails' => true,
    'secretkey' => 'someVerySecureKeyWillGoesHere',
    'db' => [
        'driver' => 'sqlite',
        'host' => '127.0.0.1',
        'port' => 9999,
        'database' => '../resources/storage/db.sqlite',
        'username' => 'db-username',
        'password' => 'db-password',
        'charset' => 'utf8',
        'collaction' => 'utf8_unicode_ci',
        'prefix' => '',
        'schema' => 'public',
        'sslmode' => 'prefer'
    ]
];
