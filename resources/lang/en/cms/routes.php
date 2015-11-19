<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CMS Routes
    |--------------------------------------------------------------------------
    |
    | Routes array
    |
    */

    '/' => [
        'slug' => '',
        'name' => 'Sign In',
        'metaTitle' => 'Sign In Title',
        'metaDescription' => 'Sign In Description',
    ],
    'signout' => [
        'slug' => 'signout',
        'name' => 'Sign Out',
        'metaTitle' => '',
        'metaDescription' => '',
        'category' => 'header',
        'parent' => false,
        'order' => 4,
        'icon' => 'remove',
        'divider-before' => true
    ],
    \Config::get('app.defaultAuthRoute') => [
        'slug' => 'dashboard',
        'name' => 'Dashboard',
        'metaTitle' => 'Dashboard Title',
        'metaDescription' => 'Dashboard Description',
        'category' => 'sidebar',
        'parent' => false,
        'order' => 1,
        'icon' => 'dashboard',
        'divider-after' => true
    ],
    'pages' => [
        'slug' => 'pages',
        'name' => 'Pages',
        'metaTitle' => 'Pages Title',
        'metaDescription' => 'Pages Description',
        'category' => 'sidebar',
        'parent' => false,
        'order' => 2,
        'icon' => 'book'
    ],
    'pf' => [
        'slug' => 'pf',
        'name' => 'Password Forgotten',
        'metaTitle' => 'Password Forgotten Title',
        'metaDescription' => 'Password Forgotten Description',
    ],
    'reset' => [
        'slug' => 'reset',
        'name' => 'Reset Password',
        'metaTitle' => 'Reset Password Title',
        'metaDescription' => 'Reset Password Description',
    ],
    'register' => [
        'slug' => 'register',
        'name' => 'Register',
        'metaTitle' => 'Register Title',
        'metaDescription' => 'Register Description',
    ],
    'users' => [
        'slug' => 'users',
        'parameters' => [
            'admins' => 'admins',
            'operators' => 'operators'
        ],
        'name' => 'Users',
        'metaTitle' => 'Users Title',
        'metaDescription' => 'Users Description',
        'category' => 'sidebar',
        'parent' => true,
        'order' => 3,
        'icon' => 'user'
    ],
    'users/' => [
        'slug' => 'users',
        'name' => 'All Users',
        'metaTitle' => 'All Users Title',
        'metaDescription' => 'All Users Description',
        'category' => 'sidebar',
        'parent' => false,
        'order' => 1
    ],
    'users/admins' => [
        'slug' => 'users',
        'name' => 'Admins',
        'metaTitle' => 'Admins Title',
        'metaDescription' => 'Admins Description',
        'category' => 'sidebar',
        'parent' => false,
        'order' => 2
    ],
    'users/operators' => [
        'slug' => 'users',
        'name' => 'Operators',
        'metaTitle' => 'Operators Title',
        'metaDescription' => 'Operators Description',
        'category' => 'sidebar',
        'parent' => false,
        'order' => 3
    ],
    'users/create' => [
        'slug' => 'users/create',
        'name' => 'Create',
        'metaTitle' => 'Create User Title',
        'metaDescription' => 'Create User Description',
    ],
     'users/destroy' => [
        'slug' => 'users/destroy',
        'name' => 'Delete Users',
        'metaTitle' => 'Are you sure that you want to permanently delete the selected users?',
        'metaDescription' => 'Are you sure that you want to permanently delete the selected users?',
    ],
    'clients' => [
        'slug' => 'clients',
        'name' => 'Clients',
        'metaTitle' => 'Clients Title',
        'metaDescription' => 'Clients Description',
        'category' => 'sidebar',
        'parent' => true,
        'order' => 4,
        'icon' => 'user'
    ],
    'clients/' => [
        'slug' => 'clients',
        'name' => 'All Clients',
        'metaTitle' => 'All Clients Title',
        'metaDescription' => 'All Clients Description',
        'category' => 'sidebar',
        'parent' => false,
        'order' => 1
    ],
    'clients/level1' => [
        'slug' => 'clients/level1',
        'name' => 'Level 1',
        'metaTitle' => 'Level 1 Title',
        'metaDescription' => 'Level 1 Description',
        'category' => 'sidebar',
        'parent' => false,
        'order' => 2
    ],
    'clients/level2' => [
        'slug' => 'clients/level2',
        'name' => 'Level 2',
        'metaTitle' => 'Level 2 Title',
        'metaDescription' => 'Level 2 Description',
        'category' => 'sidebar',
        'parent' => false,
        'order' => 3
    ],
    'profile' => [
        'slug' => 'profile',
        'name' => 'Profile',
        'metaTitle' => 'Profile Title',
        'metaDescription' => 'Profile Description',
        'category' => 'header',
        'parent' => false,
        'order' => 1,
        'icon' => 'user'
    ],
    'messages' => [
        'slug' => 'messages',
        'name' => 'Messages',
        'metaTitle' => 'Messages Title',
        'metaDescription' => 'Messages Description',
        'category' => 'header',
        'parent' => false,
        'order' => 2,
        'icon' => 'inbox'
    ],
    'settings' => [
        'slug' => 'settings',
        'name' => 'Settings',
        'metaTitle' => 'Settings Title',
        'metaDescription' => 'Settings Description',
        'category' => 'header',
        'parent' => false,
        'order' => 3,
        'icon' => 'cog'
    ],

];
