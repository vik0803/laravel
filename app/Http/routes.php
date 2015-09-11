<?php

/*
|--------------------------------------------------------------------------
| Dynamic Application Routes
|--------------------------------------------------------------------------
*/

if (Slug::match()) {
    $router->{Slug::getMethod()}(Locales::getLanguage() . '/' . Slug::getSlug() . Slug::getParameters(), Slug::getController());
}
