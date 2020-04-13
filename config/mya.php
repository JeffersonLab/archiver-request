<?php
return [
    'deployments' => ['OPS', 'DEV'],
    'administrator' => [
        'address'   => 'mya@jlab.org',
        'name'  => 'Mya Administrator'
    ],
    // The path to Staff file for username autocompletion
    'staff' => env('STAFF_FILE',storage_path('app/cebaf_staff')),
];
