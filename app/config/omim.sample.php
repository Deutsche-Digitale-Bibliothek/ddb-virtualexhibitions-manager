<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Remote Production Server Settings
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    /**
     * Make sure your current omim file matches
     * all settings under the this "common" key
     */
    'common' => array(
        'db' => array(
            'prefix' => 'omeka_exh',
            'tables' => array(
                'collections',
                'elements',
                'element_sets',
                'element_texts',
                'exhibits',
                'exhibit_pages',
                'exhibit_page_entries',
                'files',
                'items',
                'item_types',
                'item_types_elements',
                'keys',
                'options',
                'plugins',
                'processes',
                'records_tags',
                'schema_migrations',
                'search_texts',
                'sessions',
                'simple_vocab_terms',
                'tags',
                'users',
                'users_activations',
                'x3ds'
            )
        )
    ),

    /**
     * Set group under 'development' -> 'user' to the Apache user group of the local server.
     */
    'development' => array(
        'user' => array(
            'group' => 'www-data'
        )
    ),

    'remote' => array(
        // First remote server
        0 => array(
            'production' => array(
                'http' => array(
                    // The URL under which the production server is reachable.
                    // Pay attention to use HTTP or HTTPS protocol.
                    'url' => 'https://production.example.com'
                ),
                // SSH settings for remote server
                'ssh' => array(
                    // IP-address or hostname (FQDN) of the remote server
                    // If you set hostname, check your DNS and make sure
                    // the server is reachable under the specified name.
                    'host' => '127.0.0.1',
                    // SHH port to use
                    'port' => '22',
                    // SSH username
                    'username' => 'user',
                    // Path to the RSA private key.
                    // Make sure key is accessible by the user
                    // and that public part of the key is installed
                    // under authorized_keys on remote server
                    'key' => '/path/to/rsa/id_rsa',
                    // Absolute path to the remote Apache Document root (DOCUMENT_ROOT).
                    // This must point to the 'public' directory on the remote server.
                    // (Do not use trailing slashes)
                    'docroot' => '/path/to/public',
                    // Absolute path to the remote data directory.
                    // This must point to the 'data' directory on the remote server.
                    // (Do not use trailing slashes)
                    'datadir' => '/path/to/data',
                    // Group of PHP/Apache on remote server.
                    'group' => 'www-data'
                ),
                // Database settings for remote server
                'db' => array(
                    'host'      => 'localhost',
                    'database'  => 'dbname',
                    'username'  => 'dbuser',
                    'password'  => 'dbpassword',
                    'charset'   => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    // Make sure socket file exists and is accessible under given path.
                    // You may also leave this empty (e.g. if host is localhost).
                    'unix_socket' => '/var/run/mysqld/mysqld.sock',
                )
            )
        ),
        // Second remote server
        1 => array(
            'production' => array(
                'http' => array(
                    'url' => 'https://production-two.example.com'
                ),
                'ssh' => array(
                    'host' => '127.0.0.1',
                    'port' => '22',
                    'username' => 'user',
                    'key' => '/path/to/rsa/id_rsa',
                    'docroot' => '/path/to/public',
                    'datadir' => '/path/to/data',
                    'group' => 'www-data'
                ),
                'db' => array(
                    'host'      => 'localhost',
                    'database'  => 'dbname',
                    'username'  => 'dbuser',
                    'password'  => 'dbpassword',
                    'charset'   => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'unix_socket' => '/var/run/mysqld/mysqld.sock',
                )
            )
        )
    )
);