<?php

/*
|--------------------------------------------------------------------------
| Dynamic Application Routes
|--------------------------------------------------------------------------
*/

\Locales::set();

$slug = new \App\Services\Slug();
if ($slug->match()) {
    $router->{$slug->getMethod()}(\Locales::getLanguage() . '/' . $slug->getSlug(), $slug->getController());
}
