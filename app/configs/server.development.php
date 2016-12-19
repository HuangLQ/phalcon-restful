<?php

/**
 * Read more on Config Files
 * @link http://phalcon-rest.redound.org/config_files.html
 */

return [

    'debug' => true,
    'hostName' => 'http://phalconrestboilerplate',
    'clientHostName' => 'http://phalconrestboilerplate',
    'database' => [

        // Change to your own configuration
        'adapter' => 'Mysql',
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'dbname' => 'phalcon_rest_boilerplate',
    ],
    'cors' => [
        'allowedOrigins' => ['*']
    ]
];
