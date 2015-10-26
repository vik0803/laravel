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
        'category' => '',
        'parent' => false,
        'order' => 0,
        'icon' => '',
        'divider-before' => false,
        'divider-after' => false
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
        'divider-before' => true,
        'divider-after' => false
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
        'divider-before' => false,
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
        'icon' => 'book',
        'divider-before' => false,
        'divider-after' => false
    ],
    'pf' => [
        'slug' => 'pf',
        'name' => 'Password Forgotten',
        'metaTitle' => 'Password Forgotten Title',
        'metaDescription' => 'Password Forgotten Description',
        'category' => '',
        'parent' => false,
        'order' => 0,
        'icon' => '',
        'divider-before' => false,
        'divider-after' => false
    ],
    'reset' => [
        'slug' => 'reset',
        'name' => 'Reset Password',
        'metaTitle' => 'Reset Password Title',
        'metaDescription' => 'Reset Password Description',
        'category' => '',
        'parent' => false,
        'order' => 0,
        'icon' => '',
        'divider-before' => false,
        'divider-after' => false
    ],
    'register' => [
        'slug' => 'register',
        'name' => 'Register',
        'metaTitle' => 'Register Title',
        'metaDescription' => 'Register Description',
        'category' => '',
        'parent' => false,
        'order' => 0,
        'icon' => '',
        'divider-before' => false,
        'divider-after' => false
    ],
    'users' => [
        'slug' => 'users',
        'name' => 'Users',
        'metaTitle' => 'Users Title',
        'metaDescription' => 'Users Description',
        'category' => 'sidebar',
        'parent' => true,
        'order' => 3,
        'icon' => 'user',
        'divider-before' => false,
        'divider-after' => false
    ],
    'users/' => [
        'slug' => 'users',
        'name' => 'All Users',
        'metaTitle' => 'All Users Title',
        'metaDescription' => 'All Users Description',
        'category' => 'sidebar',
        'parent' => false,
        'order' => 1,
        'icon' => '',
        'divider-before' => false,
        'divider-after' => false
    ],
    'users/admins' => [
        'slug' => 'users/admins',
        'name' => 'Admins',
        'metaTitle' => 'Admins Title',
        'metaDescription' => 'Admins Description',
        'category' => 'sidebar',
        'parent' => false,
        'order' => 2,
        'icon' => '',
        'divider-before' => false,
        'divider-after' => false
    ],
    'users/admins/create' => [
        'slug' => 'users/admins/create',
        'name' => 'Admins Create',
        'metaTitle' => 'Admins Create Title',
        'metaDescription' => 'Admins Create Description',
        'category' => 'popup',
        'parent' => false,
        'order' => 1,
        'icon' => '',
        'divider-before' => false,
        'divider-after' => false
    ],
    'users/operators' => [
        'slug' => 'users/operators',
        'name' => 'Operators',
        'metaTitle' => 'Operators Title',
        'metaDescription' => 'Operators Description',
        'category' => 'sidebar',
        'parent' => false,
        'order' => 3,
        'icon' => '',
        'divider-before' => false,
        'divider-after' => false
    ],
    'users/create' => [
        'slug' => 'users/create',
        'name' => 'Create User',
        'metaTitle' => 'Create User Title',
        'metaDescription' => 'Create User Description',
        'category' => 'popup',
        'parent' => false,
        'order' => 1,
        'icon' => '',
        'divider-before' => false,
        'divider-after' => false
    ],
    'profile' => [
        'slug' => 'profile',
        'name' => 'Profile',
        'metaTitle' => 'Profile Title',
        'metaDescription' => 'Profile Description',
        'category' => 'header',
        'parent' => false,
        'order' => 1,
        'icon' => 'user',
        'divider-before' => false,
        'divider-after' => false
    ],
    'messages' => [
        'slug' => 'messages',
        'name' => 'Messages',
        'metaTitle' => 'Messages Title',
        'metaDescription' => 'Messages Description',
        'category' => 'header',
        'parent' => false,
        'order' => 2,
        'icon' => 'inbox',
        'divider-before' => false,
        'divider-after' => false
    ],
    'settings' => [
        'slug' => 'settings',
        'name' => 'Settings',
        'metaTitle' => 'Settings Title',
        'metaDescription' => 'Settings Description',
        'category' => 'header',
        'parent' => false,
        'order' => 3,
        'icon' => 'cog',
        'divider-before' => false,
        'divider-after' => false
    ],

];
