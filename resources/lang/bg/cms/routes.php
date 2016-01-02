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
        'name' => 'Вход',
        'metaTitle' => 'Вход Title',
        'metaDescription' => 'Вход Description',
    ],
    'signout' => [
        'slug' => 'изход',
        'name' => 'Изход',
        'metaTitle' => '',
        'metaDescription' => '',
        'category' => 'header',
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
        'order' => 2,
        'icon' => 'book'
    ],
    'pf' => [
        'slug' => 'забравена-парола',
        'name' => 'Забравена парола',
        'metaTitle' => 'Забравена парола Title',
        'metaDescription' => 'Забравена парола Description',
    ],
    'reset' => [
        'slug' => 'възстанови-парола',
        'name' => 'Възстанови парола',
        'metaTitle' => 'Възстанови парола Title',
        'metaDescription' => 'Възстанови парола Description',
    ],
    'register' => [
        'slug' => 'регистрация',
        'name' => 'Регистрация',
        'metaTitle' => 'Регистрация Title',
        'metaDescription' => 'Регистрация Description',
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
        'slug' => 'потребители',
        'name' => 'Всички потребители',
        'metaTitle' => 'Всички потребители Title',
        'metaDescription' => 'Всички потребители Description',
        'category' => 'sidebar',
        'order' => 1
    ],
    'users/admins' => [
        'slug' => 'потребители/администратори',
        'name' => 'Администратори',
        'metaTitle' => 'Администратори Title',
        'metaDescription' => 'Администратори Description',
        'category' => 'sidebar',
        'order' => 2
    ],
    'users/operators' => [
        'slug' => 'потребители/оператори',
        'name' => 'Оператори',
        'metaTitle' => 'Оператори Title',
        'metaDescription' => 'Оператори Description',
        'category' => 'sidebar',
        'order' => 3
    ],
    'users/create' => [
        'slug' => 'потребители/създай',
        'name' => 'Създай',
        'metaTitle' => 'Създай Потребител Title',
        'metaDescription' => 'Създай Потребител Description',
    ],
    'users/store' => [
        'slug' => 'потребители/съхрани',
    ],
    'users/edit' => [
        'slug' => 'потребители/редактирай',
        'name' => 'Edit',
        'metaTitle' => 'Edit User Title',
        'metaDescription' => 'Edit User Description',
    ],
    'users/update' => [
        'slug' => 'потребители/обнови',
    ],
    'users/delete' => [
        'slug' => 'потребители/изтрий',
        'name' => 'Delete',
        'metaTitle' => 'Are you sure that you want to permanently delete the selected users?',
        'metaDescription' => 'Are you sure that you want to permanently delete the selected users?',
    ],
    'users/destroy' => [
        'slug' => 'потребители/унищожи',
    ],
    'settings' => [
        'slug' => 'настройки',
        'name' => 'Настройки',
        'metaTitle' => 'Настройки Title',
        'metaDescription' => 'Настройки Description',
        'category' => 'sidebar',
        'parent' => true,
        'order' => 4,
        'icon' => 'cog'
    ],
    'settings/domains' => [
        'slug' => 'настройки/домейни',
        'name' => 'Домейни',
        'metaTitle' => 'Домейни Title',
        'metaDescription' => 'Домейни Description',
        'category' => 'sidebar',
        'order' => 1
    ],
    'settings/locales' => [
        'slug' => 'настройки/езици',
        'name' => 'Езици',
        'metaTitle' => 'Езици Title',
        'metaDescription' => 'Езици Description',
        'category' => 'sidebar',
        'order' => 2
    ],

];
