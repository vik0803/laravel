<?php

namespace App\Services;

use Locale;
use Stringy\StaticStringy;

class Locales
{
    protected $defaultLocale;
    protected $supportedLocales;
    protected $useAcceptLanguageHeader;
    protected $hideDefaultLocaleInURL;
    protected $currentLocale = null;
    protected $routesLocale;
    protected $routesArray = [];

    protected $subdomain;
    protected $routesPath;
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
        $this->setSubdomain(explode('.', \Request::getHost())[0]);
        $this->setRoutesPath($this->getSubdomain() . '/routes.');

        foreach (array_keys($this->supportedLocales) as $locale) {
            $this->setRoutesArray($locale, \Lang::get($this->getSubdomain() . '/routes', [], $locale));
        }
    }

    /**
     * Get the current subdomain
     *
     * @return string Returns the current subdomain
     */
    public function getSubdomain()
    {
        return $this->subdomain;
    }

    /**
     * Set the current subdomain
     *
     * @return void
     */
    public function setSubdomain($subdomain)
    {
        $this->subdomain = $subdomain;
    }

    /**
     * Get routes path
     *
     * @return string Returns the routes path
     */
    public function getRoutesPath()
    {
        return $this->routesPath;
    }

    /**
     * Set the routes path
     *
     * @return void
     */
    public function setRoutesPath($path)
    {
        $this->routesPath = $path;
    }

    /**
     * Get routes array for a given locale or all routes
     *
     * @return array Returns routes array
     */
    public function getRoutesArray($locale = null)
    {
        return $locale ? $this->routesArray[$locale] : $this->routesArray;
    }

    /**
     * Set routes array for all locales
     *
     * @return void
     */
    public function setRoutesArray($locale, $routes)
    {
        $this->routesArray[$locale] = $routes;
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
     * Get route parameters
     *
     * @return array Returns route parameters
     */
    public function getRouteParameters($route, $locale = null)
    {
        $locale = $locale ?: $this->getCurrent();
        $parameters = [];
        if (\Lang::hasForLocale($this->getRoutesPath() . $route . '.parameters', $locale)) {
            $parameters = \Lang::get($this->getRoutesPath() . $route . '.parameters', [], $locale);
        }
        return $parameters;
    }

    /**
     * Get route default parameters
     *
     * @return string|boolean Returns default route parameters or false
     */
    public function getDefaultParameter($route, $parameter)
    {
        return array_search($parameter, $this->getRouteParameters($route));
    }

    /**
     * Get current route Meta Title
     *
     * @return string Returns Meta Title of the current route
     */
    public function getMetaTitle()
    {
        if (\Lang::hasForLocale($this->getRoutesPath() . \Slug::getRouteSlug(), $this->getCurrent())) {
            return trans($this->getRoutesPath() . \Slug::getRouteSlug() . '.metaTitle');
        } else {
            return trans($this->getRoutesPath() . \Slug::getRouteName() . '.metaTitle');
        }
    }

    /**
     * Get current route Meta Description
     *
     * @return string Returns Meta Description of the current route
     */
    public function getMetaDescription()
    {
        if (\Lang::hasForLocale($this->getRoutesPath() . \Slug::getRouteSlug(), $this->getCurrent())) {
            return trans($this->getRoutesPath() . \Slug::getRouteSlug() . '.metaDescription');
        } else {
            return trans($this->getRoutesPath() . \Slug::getRouteName() . '.metaDescription');
        }
    }

    /**
     * Get current language for links
     *
     * @return string Returns current locale
     */
    public function getLanguage($locale = null)
    {
        $locale = $locale ?: $this->getCurrent();
        return ($this->hideDefaultLocaleInURL && $locale == $this->getDefault()) ? '' : $locale . '/';
    }

    /**
     * Get language translation for a given route
     *
     * @return string Returns translated route
     */
    public function getRoute($route, $prefix = true)
    {
        return ($prefix ? $this->getLanguage($this->getRoutesLocale()) : '') . \Lang::get($this->getRoutesPath() . $route . '.slug', [], $this->getRoutesLocale());
    }

    /**
     * Check if a given route is defined
     *
     * @return boolean
     */
    public function isRoute($route)
    {
        return \Lang::hasForLocale($this->getRoutesPath() . $route, $this->getRoutesLocale());
    }

    /**
     * Get constraints translation for a given route
     *
     * @return string Returns translated route constraints
     */
    public function getRouteRegex($route)
    {
        return implode('|', $this->getRouteParameters($route, $this->getRoutesLocale()));
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
     * Get route locale name
     *
     * @return string Returns route locale name
     */
    public function getRouteName($name = '')
    {
        return $this->getLanguage($this->getRoutesLocale()) . $name;
    }

    /**
     * Filter routes array
     *
     * @return array Returns all keys starting with '$key/''
     */
    public function filterRoutes($routes, $key)
    {
        return array_filter($routes, function($k) use ($key) {
            return strpos($k, $key . '/') === 0;
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Get localized parameters from current route
     *
     * @return string
     */
    public function rawParameters($locale = null)
    {
        $locale = $locale ?: $this->getCurrent();
        $parameters = [];
        $key = '';
        $langLocale = ($this->getCurrent() == $this->getDefault() ? $locale : $this->getCurrent());

        $params = $this->getRouteParameters(\Slug::getRouteName(), $langLocale);
        if (empty($params)) {
            $slugs = explode('/', \Slug::getRouteName());
            for ($i = count($slugs) - 1; $i >= 0; $i--) {
                $params = $this->getRouteParameters($slugs[$i], $langLocale);
                if (!empty($params)) {
                    break;
                }
            }
        }

        if ($this->getCurrent() == $this->getDefault()) {
            foreach (\Slug::getRouteParameters() as $name => $value) {
                if (array_key_exists($value, $params)) {
                    $parameters[$name] = $params[$value];
                }
            }
        } else {
            foreach (\Slug::getRouteParameters() as $name => $value) {
                if (($key = array_search($value, $params)) !== false) {
                    if ($locale == $this->getCurrent()) {
                        $parameters[$name] = $params[$key];
                    } else {
                        $parameters[$name] = $key;
                    }
                }
            }
        }

        return $parameters;
    }

    /**
     * Get localized url from current slug
     *
     * @return string
     */
    public function rawUrl($locale = null)
    {
        $locale = $locale ?: $this->getCurrent();

        $routes = $this->getRoutesArray($this->getCurrent() == $this->getDefault() ? $locale : $this->getCurrent());
        $slugs = \Slug::getSlugs();

        $slug = '';
        $key = '';
        if ($this->getCurrent() == $this->getDefault()) {
            for ($i = 0; $i < count($slugs); $i++) {
                $key .= ($i ? '/' : '') . $slugs[$i];
                if (array_key_exists($key, $routes)) {
                    $slug .= $routes[$key]['slug'] . '/';
                    $routes = $this->filterRoutes($routes, $key);
                }
            }
        } else {
            for ($i = 0; $i < count($slugs); $i++) {
                if (($key = array_search($slugs[$i], array_column($routes, 'slug'))) !== false) {
                    $key = array_keys($routes)[$key];

                    if ($locale == $this->getCurrent()) {
                        $slug .= $routes[$key]['slug'] . '/'; // $slugs[$i]
                    } else {
                        $slug = $key;
                    }
                    $routes = $this->filterRoutes($routes, $key);
                }
            }
        }

        return url(rtrim($this->getLanguage($locale) . $slug, '/'));
    }

    /**
     * Get localized url from current route
     *
     * @return string
     */
    public function url($locale = null)
    {
        $locale = $locale ?: $this->getCurrent();
        $prefix = $this->getLanguage($locale);
        $slug = $prefix . \Slug::getRouteName();
        $parameters = $this->rawParameters($locale);

        return \Route::has($slug) ? route($slug, $parameters) : route($prefix . \Config::get('app.defaultAuthRoute'), $parameters);
    }

    /**
     * Get localized route
     *
     * @return string
     */
    public function route($route = null, $parameters = null) {
        $route = $this->getLanguage() . ($route ?: \Config::get('app.defaultAuthRoute'));

        if ($parameters === true) {
            $parameters = \Slug::getRouteParameters();
        }

        return \Route::has($route) ? ($parameters ? route($route, $parameters) : route($route)) : '';
    }

    /**
     * Create Breadcrumbs array from route slugs
     *
     * @return array
     */
    public function createBreadcrumbsFromSlugs($slugs = null) {
        $slugs = $slugs ?: explode('/', \Slug::getRouteName());
        $lastSlug = last($slugs);
        $breadcrumbs = [];
        $breadcrumbPath = '';

        if (head($slugs) != \Config::get('app.defaultAuthRoute')) {
            array_unshift($slugs, \Config::get('app.defaultAuthRoute'));
        }

        foreach ($slugs as $slug) {
            $breadcrumbPath = trim($breadcrumbPath . '/' . $slug, '/');
            $link = $this->route($breadcrumbPath);
            $last = ($slug == $lastSlug ? true : false);

            if (\Lang::hasForLocale($this->getRoutesPath() . $slug . '/', $this->getCurrent())) { // 'slug/' == dropdowm
                $breadcrumbs[$slug]['link'] = $link . '#'; // dropdown
                $breadcrumbs[$slug]['name'] = trans($this->getRoutesPath() . $breadcrumbPath . '.name');
                $breadcrumbs[$slug]['last'] = false;

                if ($last) {
                    if (\Lang::hasForLocale($this->getRoutesPath() . $slug . '.parameters', $this->getCurrent())) {
                        $link = $this->route($slug, true);
                        $slug = \Slug::getRouteSlug();
                    } else {
                        $slug .= '/';
                    }

                    $breadcrumbs[$slug]['link'] = $link;
                    $breadcrumbs[$slug]['name'] = trans($this->getRoutesPath() . $slug . '.name');
                    $breadcrumbs[$slug]['last'] = $last;
                }
            } else {
                if ($last) {
                    $link = $this->route($breadcrumbPath, true);
                }

                $breadcrumbs[$slug]['link'] = $link;
                $breadcrumbs[$slug]['name'] = trans($this->getRoutesPath() . $breadcrumbPath . '.name');
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
     * Get navigation array
     *
     * @return array
     */
    public function getNavigation($category) {
        $routes = array_where($this->getRoutesArray($this->getCurrent()), function ($key, $value) use ($category) {
            return $value['category'] == $category;
        });
        ksort($routes);

        $navigation = $this->getNavigationRecursive($routes);

        return $navigation;
    }

    /**
     * Get navigation array recursively
     *
     * @return array
     */
    public function getNavigationRecursive($routes, $parameters = null, $i = 1) {
        $keys = [];
        $navigation = [];

        foreach ($routes as $slug => $route) {
            if (!in_array($slug, $keys)) {
                if ($route['parent']) {
                    $link = $this->route($slug) . '#';
                    $active = \Slug::isActive(last(explode('/', $slug)), $i);

                    $subRoutes = $this->filterRoutes($routes, $slug);

                    $parameters = null;
                    if (isset($route['parameters'])) {
                        $parameters = $route['parameters'];
                    }

                    $navigation[$route['order']]['children'] = $this->getNavigationRecursive($subRoutes, $parameters, $i + 1);

                    $keys = array_merge($keys, array_keys($subRoutes));
                } else {
                    $key = StaticStringy::removeLeft($slug, $route['slug'] . '/');
                    if ($parameters && array_key_exists($key, $parameters)) {
                        $link = $this->route($route['slug'], $parameters[$key]);
                        $active = \Slug::isActive($route['slug'] . '/' . $key);
                    } else {
                        if (ends_with($slug, '/')) {
                            $slug = StaticStringy::removeRight($slug, '/');
                        }
                        $link = $this->route($slug);
                        $active = \Slug::isActive($slug);
                    }
                }

                $navigation[$route['order']]['level'] = $i;
                $navigation[$route['order']]['link'] = $link;
                $navigation[$route['order']]['active'] = $active;
                $navigation[$route['order']]['name'] = $route['name'];
                $navigation[$route['order']]['icon'] = isset($route['icon']) ? $route['icon'] : null;
                $navigation[$route['order']]['divider-before'] = isset($route['divider-before']) ? $route['divider-before'] : false;
                $navigation[$route['order']]['divider-after'] = isset($route['divider-after']) ? $route['divider-after'] : false;
            }
        }
        ksort($navigation);
        return $navigation;
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
