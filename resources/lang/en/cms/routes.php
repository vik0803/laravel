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
        'order' => 4,
        'icon' => 'remove',
        'divider-before' => true,
    ],
    'dashboard' => [
        'slug' => 'dashboard',
        'name' => 'Dashboard',
        'metaTitle' => 'Dashboard Title',
        'metaDescription' => 'Dashboard Description',
        'category' => 'sidebar-menu',
        'order' => 1,
        'icon' => 'dashboard',
    ],
    'pages' => [
        'slug' => 'pages',
        'name' => 'Pages',
        'metaTitle' => 'Pages Title',
        'metaDescription' => 'Pages Description',
        'category' => 'sidebar-menu',
        'order' => 2,
        'icon' => 'book',
    ],
    'pages/create' => [
        'slug' => 'pages/create',
        'name' => 'Create',
        'metaTitle' => 'Create Page Title',
        'metaDescription' => 'Create Page Description',
    ],
    'pages/create-category' => [
        'slug' => 'pages/create-category',
        'name' => 'Create Category',
        'metaTitle' => 'Create Category Title',
        'metaDescription' => 'Create Category Description',
    ],
    'pages/store' => [
        'slug' => 'pages/store',
    ],
    'pages/edit' => [
        'slug' => 'pages/edit',
        'name' => 'Edit',
        'metaTitle' => 'Edit Page Title',
        'metaDescription' => 'Edit Page Description',
    ],
    'pages/update' => [
        'slug' => 'pages/update',
    ],
    'pages/delete' => [
        'slug' => 'pages/delete',
        'name' => 'Delete',
        'metaTitle' => 'Are you sure that you want to permanently delete the selected pages?',
        'metaDescription' => 'Are you sure that you want to permanently delete the selected pages?',
    ],
    'pages/destroy' => [
        'slug' => 'pages/destroy',
    ],
    'pages/upload' => [
        'slug' => 'pages/upload',
    ],
    'pages/delete-image' => [
        'slug' => 'pages/delete-image',
        'name' => 'Delete Image',
        'metaTitle' => 'Are you sure that you want to permanently delete the selected images?',
        'metaDescription' => 'Are you sure that you want to permanently delete the selected images?',
    ],
    'pages/destroy-image' => [
        'slug' => 'pages/destroy-image',
    ],
    'pages/edit-image' => [
        'slug' => 'pages/edit-image',
        'name' => 'Edit Image',
        'metaTitle' => 'Edit Image Title',
        'metaDescription' => 'Edit Image Description',
    ],
    'pages/update-image' => [
        'slug' => 'pages/update-image',
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
            'operators' => 'operators',
        ],
        'name' => 'Users',
        'metaTitle' => 'Users Title',
        'metaDescription' => 'Users Description',
        'category' => 'sidebar-menu',
        'parent' => true,
        'order' => 3,
        'icon' => 'user',
    ],
    'users/' => [
        'slug' => 'users',
        'name' => 'All Users',
        'metaTitle' => 'All Users Title',
        'metaDescription' => 'All Users Description',
        'category' => 'sidebar-menu',
        'order' => 1,
    ],
    'users/admins' => [
        'slug' => 'users/admins',
        'name' => 'Admins',
        'metaTitle' => 'Admins Title',
        'metaDescription' => 'Admins Description',
        'category' => 'sidebar-menu',
        'order' => 2,
    ],
    'users/operators' => [
        'slug' => 'users/operators',
        'name' => 'Operators',
        'metaTitle' => 'Operators Title',
        'metaDescription' => 'Operators Description',
        'category' => 'sidebar-menu',
        'order' => 3,
    ],
    'users/create' => [
        'slug' => 'users/create',
        'name' => 'Create',
        'metaTitle' => 'Create User Title',
        'metaDescription' => 'Create User Description',
    ],
    'users/store' => [
        'slug' => 'users/store',
    ],
    'users/edit' => [
        'slug' => 'users/edit',
        'name' => 'Edit',
        'metaTitle' => 'Edit User Title',
        'metaDescription' => 'Edit User Description',
    ],
    'users/update' => [
        'slug' => 'users/update',
    ],
    'users/delete' => [
        'slug' => 'users/delete',
        'name' => 'Delete',
        'metaTitle' => 'Are you sure that you want to permanently delete the selected users?',
        'metaDescription' => 'Are you sure that you want to permanently delete the selected users?',
    ],
    'users/destroy' => [
        'slug' => 'users/destroy',
    ],
    'settings' => [
        'slug' => 'settings',
        'name' => 'Settings',
        'metaTitle' => 'Settings Title',
        'metaDescription' => 'Settings Description',
        'category' => 'sidebar-settings',
        'parent' => true,
        'order' => 4,
        'icon' => 'cog',
    ],
    'settings/domains' => [
        'slug' => 'settings/domains',
        'name' => 'Domains',
        'metaTitle' => 'Domains Title',
        'metaDescription' => 'Domains Description',
        'category' => 'sidebar-settings',
        'order' => 1,
    ],
    'settings/domains/create' => [
        'slug' => 'settings/domains/create',
        'name' => 'Create',
        'metaTitle' => 'Create Domain Title',
        'metaDescription' => 'Create Domain Description',
    ],
    'settings/domains/store' => [
        'slug' => 'settings/domains/store',
    ],
    'settings/domains/edit' => [
        'slug' => 'settings/domains/edit',
        'name' => 'Edit',
        'metaTitle' => 'Edit Domain Title',
        'metaDescription' => 'Edit Domain Description',
    ],
    'settings/domains/update' => [
        'slug' => 'settings/domains/update',
    ],
    'settings/domains/delete' => [
        'slug' => 'settings/domains/delete',
        'name' => 'Delete',
        'metaTitle' => 'Are you sure that you want to permanently delete the selected domains?',
        'metaDescription' => 'Are you sure that you want to permanently delete the selected domains?',
    ],
    'settings/domains/destroy' => [
        'slug' => 'settings/domains/destroy',
    ],
    'settings/locales' => [
        'slug' => 'settings/locales',
        'name' => 'Locales',
        'metaTitle' => 'Locales Title',
        'metaDescription' => 'Locales Description',
        'category' => 'sidebar-settings',
        'order' => 2,
    ],
    'settings/locales/create' => [
        'slug' => 'settings/locales/create',
        'name' => 'Create',
        'metaTitle' => 'Create Locale Title',
        'metaDescription' => 'Create Locale Description',
    ],
    'settings/locales/store' => [
        'slug' => 'settings/locales/store',
    ],
    'settings/locales/edit' => [
        'slug' => 'settings/locales/edit',
        'name' => 'Edit',
        'metaTitle' => 'Edit Locale Title',
        'metaDescription' => 'Edit Locale Description',
    ],
    'settings/locales/update' => [
        'slug' => 'settings/locales/update',
    ],
    'settings/locales/delete' => [
        'slug' => 'settings/locales/delete',
        'name' => 'Delete',
        'metaTitle' => 'Are you sure that you want to permanently delete the selected locales?',
        'metaDescription' => 'Are you sure that you want to permanently delete the selected locales?',
    ],
    'settings/locales/destroy' => [
        'slug' => 'settings/locales/destroy',
    ],

];
