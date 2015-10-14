<?php

namespace App\Services;

use Locale;

class Locales
{
    protected $defaultLocale;
    protected $supportedLocales;
    protected $useAcceptLanguageHeader;
    protected $hideDefaultLocaleInURL;
    protected $currentLocale = null;
    protected $routesLocale;

    protected $routes;
    protected $subdomain;
    protected $slug;
    protected $slugs;

    /**
     * Creates new instance.
     */
    public function __construct()
    {
        $this->defaultLocale = \Config::get('app.fallback_locale');
        $this->supportedLocales = \Config::get('locales.supportedLocales');
        $this->useAcceptLanguageHeader = \Config::get('locales.useAcceptLanguageHeader');
        $this->hideDefaultLocaleInURL = \Config::get('locales.hideDefaultLocaleInURL');
        $this->subdomain = explode('.', \Request::getHost())[0];
    }

    /**
     * Get current locale
     *
     * @return string Returns current locale
     */
    public function getCurrent()
    {
        return $this->currentLocale;
    }

    /**
     * Set current locale
     *
     * @return void
     */
    public function setCurrent($locale)
    {
        $this->currentLocale = $locale;
    }

    /**
     * Get current locale script
     *
     * @return string Returns current locale script
     */
    public function getScript()
    {
        return $this->supportedLocales[$this->getCurrent()]['script'];
    }

    /**
     * Get current locale name
     *
     * @return string Returns current locale name
     */
    public function getName()
    {
        return $this->supportedLocales[$this->getCurrent()]['name'];
    }

    /**
     * Get current locale native name
     *
     * @return string Returns current locale native name
     */
    public function getNativeName()
    {
        return $this->supportedLocales[$this->getCurrent()]['native'];
    }

    /**
     * Get default locale
     *
     * @return string Returns default locale
     */
    public function getDefault()
    {
        return $this->defaultLocale;
    }

    /**
     * Get all supported locales
     *
     * @return array Returns all supported locales
     */
    public function getSupportedLocales()
    {
        return $this->supportedLocales;
    }

    /**
     * Get current language for links
     *
     * @return string Returns current locale
     */
    public function getLanguage($locale = null)
    {
        $locale = $locale ?: $this->getCurrent();
        return ($this->hideDefaultLocaleInURL && $locale == $this->getDefault()) ? '' : $locale;
    }

    /**
     * Get language translation for a given route
     *
     * @return string Returns translated route
     */
    public function getRoute($route)
    {
        return $this->getRoutesLocalePrefix() . \Lang::get($this->subdomain . '/routes.' . $route, [], $this->getRoutesLocale());
    }

    /**
     * Get route locale
     *
     * @return string Returns current route locale
     */
    public function getRoutesLocale()
    {
        return $this->routesLocale;
    }

    /**
     * Set route locale
     *
     * @return void
     */
    public function setRoutesLocale($locale)
    {
        return $this->routesLocale = $locale;
    }

    /**
     * Get route locale prefix
     *
     * @return string Returns current route locale prefix
     */
    public function getRoutesLocalePrefix()
    {
        return (($this->hideDefaultLocaleInURL && $this->getRoutesLocale() == $this->getDefault()) ? '' : $this->getRoutesLocale()) . $this->separator($this->getRoutesLocale());
    }

    /**
     * Get language translation for a given sub route
     *
     * @return string Returns translated route
     */
    public function getSubRoute($route)
    {
        return \Lang::get($this->subdomain . '/routes.' . $route, [], $this->getRoutesLocale());
    }

    /**
     * Get localized url from current slug
     *
     * @return string
     */
    public function rawUrl($locale = null)
    {
        $locale = $locale ?: $this->getCurrent();
        $url = ($locale == $this->getDefault() ? $this->getLanguage($locale) : $locale) . '/';

        $this->routes = \Lang::get($this->subdomain . '/routes', [], ($this->getCurrent() == $this->getDefault() ? $locale : $this->getCurrent()));
        $slugs = \Slug::getSlugs();

        $slug = '';
        $key = '';
        if ($this->getCurrent() == $this->getDefault() && $locale != $this->getDefault()) {
            for ($i = 0; $i < count($slugs); $i++) {
                $key .= ($i ? '/' : '') . $slugs[$i];
                if (array_key_exists($key, $this->routes)) {
                    $slug .= $this->routes[$key] . '/';
                    $this->routes = array_filter($this->routes, function($k) use ($key) {
                        return strpos($k, $key . '/') === 0;
                    }, ARRAY_FILTER_USE_KEY);
                }
            }
        } else {
            for ($i = 0; $i < count($slugs); $i++) {
                if (($key = array_search($slugs[$i], $this->routes)) !== false) {
                    if ($locale == $this->getCurrent()) {
                        $slug .= $this->routes[$key] . '/'; // = $slugs[$i]
                    } else {
                        $slug = $key;
                    }
                    $this->routes = array_filter($this->routes, function($k) use ($key) {
                        return strpos($k, $key . '/') === 0;
                    }, ARRAY_FILTER_USE_KEY);
                }
            }
        }

        return url(rtrim($url . $slug, '/'));
    }

    /**
     * Get localized url from current route
     *
     * @return string
     */
    public function url($locale = null)
    {
        $locale = $locale ?: $this->getCurrent();
        $route = $this->getLanguage($locale) . $this->separator($locale);
        $route .= \Route::has($route . \Slug::getRouteSlug()) ? \Slug::getRouteSlug() : \Config::get('app.defaultAuthRoute');
        $currentRoute = \Route::current();
        $params = $currentRoute ? $currentRoute->parameters() : ''; // \Request::route()->parameters()
        return \Route::has($route) ? route($route, $params) : '';
    }

    /**
     * Get localized route
     *
     * @return string
     */
    public function route($route = null) {
        $route = $route ?: \Config::get('app.defaultAuthRoute');
        $route = $this->getLanguage() . $this->separator() . $route;
        $currentRoute = \Route::current();
        $params = $currentRoute ? $currentRoute->parameters() : ''; // \Request::route()->parameters()
        return \Route::has($route) ? route($route, $params) : '';
    }

    /**
     * Get locale separator
     *
     * @return string
     */
    public function separator($locale = null) {
        $locale = $locale ?: $this->getCurrent();
        return ($this->hideDefaultLocaleInURL && $locale == $this->getDefault()) ? '' : '/';
    }

    /**
     * Create Breadcrumbs array from route slugs
     *
     * @return array
     */
    public function createBreadcrumbsFromSlugs($slugs = null) {
        $slugs = $slugs ?: \Slug::getRouteSlugs();
        $breadcrumbs = [];
        $breadcrumbPath = '';

        if (head($slugs) != \Config::get('app.defaultAuthRoute')) {
            array_unshift($slugs, \Config::get('app.defaultAuthRoute'));
        }

        foreach ($slugs as $slug) {
            $breadcrumbPath = trim($breadcrumbPath . '/' . $slug, '/');
            $link = $this->route($breadcrumbPath);

            $last = ($slug == last($slugs) ? true : false);

            $parentSlug = $slug . '/';
            if (\Lang::has('cms/routes.' . $parentSlug)) { // translation for 'slug/' == dropdowm
                $breadcrumbs[$parentSlug]['link'] = '#'; // dropdown
                $breadcrumbs[$parentSlug]['name'] = trans('cms/routes.' . $breadcrumbPath);
                $breadcrumbs[$parentSlug]['last'] = false;

                if ($last) {
                    $breadcrumbs[$slug]['link'] = $link;
                    $breadcrumbs[$slug]['name'] = trans('cms/routes.' . $parentSlug);
                    $breadcrumbs[$slug]['last'] = $last;
                }
            } else {
                $breadcrumbs[$slug]['link'] = $link;
                $breadcrumbs[$slug]['name'] = trans('cms/routes.' . $breadcrumbPath);
                $breadcrumbs[$slug]['last'] = $last;
            }

            if ($breadcrumbPath == \Config::get('app.defaultAuthRoute')) {
                $breadcrumbPath = '';
            }
        }

        return $breadcrumbs;
    }

    /**
     * Get Languages
     *
     * @return array
     */
    public function getLanguages() {
        $languages = [];

        foreach ($this->getSupportedLocales() as $locale => $data) {
            $active = ($locale == $this->getCurrent() ? true : false);
            $language['active'] = $active;
            $language['link'] = $this->url($locale);
            $language['native'] = $data['native'];
            $language['name'] = ($data['name'] != $data['native'] ? $data['name'] : '');

            if ($active) {
                $languages = array_merge([$locale => $language], $languages);
            } else {
                $languages[$locale] = $language;
            }
        }

        return $languages;
    }

    /**
     * Set current locale
     *
     * @param string $locale Locale to set the App to (optional)
     *
     * @return void
     */
    public function set($locale = null)
    {
        if (empty($locale) || !is_string($locale)) {
            // If the locale has not been passed through the function it tries to get it from the first segment of the url
            $locale = \Request::segment(1);
        }

        if (isset($this->supportedLocales[$locale])) {
            $this->setCurrent($locale);
        } else {
            // if the first segment/locale passed is not valid the locale could be taken by the browser depending on your configuration
            $locale = null;

            // if we reached this point and hideDefaultLocaleInURL is true we have to assume we are routing to a defaultLocale route.
            if ($this->hideDefaultLocaleInURL) {
                $this->setCurrent($this->getDefault());
            } elseif ($this->useAcceptLanguageHeader) { // but if hideDefaultLocaleInURL is false && useAcceptLanguageHeader is true, we have to retrieve it from the browser...
                $this->setCurrent($this->negotiateLanguage());
            } else { // or just get application default locale
                $this->setCurrent($this->getDefault());
            }
        }

        \App::setLocale($this->getCurrent());
    }

    /**
     * Negotiates language with the user's browser through the Accept-Language
     * HTTP header or the user's host address. Language codes are generally in
     * the form "ll" for a language spoken in only one country, or "ll-CC" for a
     * language spoken in a particular country. For example, U.S. English is
     * "en-US", while British English is "en-UK". Portuguese as spoken in
     * Portugal is "pt-PT", while Brazilian Portuguese is "pt-BR".
     *
     * This function is based on negotiateLanguage from Pear HTTP2
     * http://pear.php.net/package/HTTP2/
     *
     * Quality factors in the Accept-Language: header are supported, e.g.:
     * Accept-Language: en-UK;q=0.7, en-US;q=0.6, no, dk;q=0.8
     *
     * @return string The negotiated language result or app.locale.
     */
    public function negotiateLanguage()
    {
        $matches = $this->getMatchesFromAcceptedLanguages();
        foreach ($matches as $key => $q) {
            if (isset($this->supportedLocales[$key])) {
                return $key;
            }
        }
        // If any (i.e. "*") is acceptable, return the default locale
        if (isset($matches['*'])) {
            return $this->getDefault();
        }

        if (class_exists('Locale') && !empty(\Request::server('HTTP_ACCEPT_LANGUAGE'))) {
            $http_accept_language = Locale::acceptFromHttp(\Request::server('HTTP_ACCEPT_LANGUAGE'));

            if (isset($this->supportedLocales[$http_accept_language])) {
                return $http_accept_language;
            }
        }

        if (\Request::server('REMOTE_HOST')) {
            $remote_host = explode('.', \Request::server('REMOTE_HOST'));
            $lang = strtolower(end($remote_host));

            if (isset($this->supportedLocales[$lang])) {
                return $lang;
            }
        }

        return $this->getDefault();
    }

    /**
     * Return all the accepted languages from the browser
     *
     * @return array Matches from the header field Accept-Languages
     */
    private function getMatchesFromAcceptedLanguages()
    {
        $matches = [];

        if ($acceptLanguages = \Request::header('Accept-Language')) {
            $acceptLanguages = explode(',', $acceptLanguages);

            $generic_matches = [];
            foreach ($acceptLanguages as $option) {
                $option = array_map('trim', explode(';', $option));
                $l = $option[0];
                if (isset($option[1])) {
                    $q = (float)str_replace('q=', '', $option[1]);
                } else {
                    $q = null;
                    // Assign default low weight for generic values
                    if ($l == '*/*') {
                        $q = 0.01;
                    } elseif (substr($l, -1) == '*') {
                        $q = 0.02;
                    }
                }
                // Unweighted values, get high weight by their position in the list
                $q = (isset($q) ? $q : 1000) - count($matches);
                $matches[$l] = $q;

                // If for some reason the Accept-Language header only sends language with country
                // we should make the language without country an accepted option, with a value
                // less than it's parent.
                $l_ops = explode('-', $l);
                array_pop($l_ops);
                while (!empty($l_ops)) {
                    // The new generic option needs to be slightly less important than it's base
                    $q -= 0.001;
                    $op = implode('-', $l_ops);
                    if (empty($generic_matches[$op]) || $generic_matches[$op] > $q) {
                        $generic_matches[$op] = $q;
                    }
                    array_pop($l_ops);
                }
            }
            $matches = array_merge($generic_matches, $matches);

            arsort($matches, SORT_NUMERIC);
        }

        return $matches;
    }
}
