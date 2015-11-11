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
        'order' => 0
    ],
    'signout' => [
        'slug' => 'изход',
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
        'slug' => 'начало',
        'name' => 'Начало',
        'metaTitle' => 'Начало Title',
        'metaDescription' => 'Начало Description',
        'category' => 'sidebar',
        'parent' => false,
        'order' => 1,
        'icon' => 'dashboard',
        'divider-after' => true
    ],
    'pages' => [
        'slug' => 'страници',
        'name' => 'Страници',
        'metaTitle' => 'Страници Title',
        'metaDescription' => 'Страници Description',
        'category' => 'sidebar',
        'parent' => false,
        'order' => 2,
        'icon' => 'book'
    ],
    'pf' => [
        'slug' => 'забравена-парола',
        'name' => 'Password Forgotten',
        'metaTitle' => 'Password Forgotten Title',
        'metaDescription' => 'Password Forgotten Description',
        'category' => '',
        'parent' => false,
        'order' => 0
    ],
    'reset' => [
        'slug' => 'възстанови-парола',
        'name' => 'Reset Password',
        'metaTitle' => 'Reset Password Title',
        'metaDescription' => 'Reset Password Description',
        'category' => '',
        'parent' => false,
        'order' => 0
    ],
    'register' => [
        'slug' => 'регистрация',
        'name' => 'Register',
        'metaTitle' => 'Register Title',
        'metaDescription' => 'Register Description',
        'category' => '',
        'parent' => false,
        'order' => 0
    ],
    'users' => [
        'slug' => 'потребители',
        'parameters' => [
            'admins' => 'администратори',
            'operators' => 'оператори'
        ],
        'name' => 'Потребители',
        'metaTitle' => 'Потребители Title',
        'metaDescription' => 'Потребители Description',
        'category' => 'sidebar',
        'parent' => true,
        'order' => 3,
        'icon' => 'user'
    ],
    'users/' => [
        'slug' => 'users',
        'name' => 'Всички потребители',
        'metaTitle' => 'Всички потребители Title',
        'metaDescription' => 'Всички потребители Description',
        'category' => 'sidebar',
        'parent' => false,
        'order' => 1
    ],
    'users/admins' => [
        'slug' => 'users',
        'name' => 'Администратори',
        'metaTitle' => 'Администратори Title',
        'metaDescription' => 'Администратори Description',
        'category' => 'sidebar',
        'parent' => false,
        'order' => 2
    ],
    'users/operators' => [
        'slug' => 'users',
        'name' => 'Оператори',
        'metaTitle' => 'Оператори Title',
        'metaDescription' => 'Оператори Description',
        'category' => 'sidebar',
        'parent' => false,
        'order' => 3
    ],
    'users/create' => [
        'slug' => 'потребители/създай',
        'name' => 'Създай',
        'metaTitle' => 'Създай Потребител Title',
        'metaDescription' => 'Създай Потребител Description',
        'category' => 'popup',
        'parent' => false,
        'order' => 0
    ],
    'clients' => [
        'slug' => 'клиенти',
        'name' => 'Клиенти',
        'metaTitle' => 'Клиенти Title',
        'metaDescription' => 'Клиенти Description',
        'category' => 'sidebar',
        'parent' => true,
        'order' => 4,
        'icon' => 'user'
    ],
    'clients/' => [
        'slug' => 'клиенти',
        'name' => 'Всички клиенти',
        'metaTitle' => 'Всички клиенти Title',
        'metaDescription' => 'Всички клиенти Description',
        'category' => 'sidebar',
        'parent' => false,
        'order' => 1
    ],
    'clients/level1' => [
        'slug' => 'клиенти/ниво1',
        'name' => 'Ниво 1',
        'metaTitle' => 'Ниво 1 Title',
        'metaDescription' => 'Ниво 1 Description',
        'category' => 'sidebar',
        'parent' => false,
        'order' => 2
    ],
    'clients/level2' => [
        'slug' => 'клиенти/ниво2',
        'name' => 'Ниво 2',
        'metaTitle' => 'Ниво 2 Title',
        'metaDescription' => 'Ниво 2 Description',
        'category' => 'sidebar',
        'parent' => false,
        'order' => 3
    ],
    'profile' => [
        'slug' => 'профил',
        'name' => 'Profile',
        'metaTitle' => 'Profile Title',
        'metaDescription' => 'Profile Description',
        'category' => 'header',
        'parent' => false,
        'order' => 1,
        'icon' => 'user'
    ],
    'messages' => [
        'slug' => 'съобщения',
        'name' => 'Messages',
        'metaTitle' => 'Messages Title',
        'metaDescription' => 'Messages Description',
        'category' => 'header',
        'parent' => false,
        'order' => 2,
        'icon' => 'inbox'
    ],
    'settings' => [
        'slug' => 'настройки',
        'name' => 'Settings',
        'metaTitle' => 'Settings Title',
        'metaDescription' => 'Settings Description',
        'category' => 'header',
        'parent' => false,
        'order' => 3,
        'icon' => 'cog'
    ],

];
