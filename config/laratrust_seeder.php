<?php

return [
    /**
     * Control if the seeder should create a user per role while seeding the data.
     */
    'create_users' => false,

    /**
     * Control if all the laratrust tables should be truncated before running the seeder.
     */
    'truncate_tables' => true,

    'roles_structure' => [
        'direktur' => [
            'users' => 'c,r,u,d',
        ],
        'admin' => [
            'users' => 'c,r,u,d',
        ],
        'kasir' => [
            'users' => 'c,r,u,d',
        ],
        'capster' => [
            'users' => 'c,r,u,d',
        ],
    ],

    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete',
    ],
];
