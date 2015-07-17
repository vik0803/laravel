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

    protected $routes;
    protected $subdomain;
    protected $slug;
    protected $slugs;

    /**
     * Creates new instance.
     */
    public function __construct()
    {
        $this->defaultLocale = \Config::get('app.locale');
        $this->supportedLocales = \Config::get('locales.supportedLocales');
        $this->useAcceptLanguageHeader = \Config::get('locales.useAcceptLanguageHeader');
        $this->hideDefaultLocaleInURL = \Config::get('locales.hideDefaultLocaleInURL');
    }

    /**
     * Get current locale
     *
     * @return string Returns current locale
     */
    public function get()
    {
        return $this->currentLocale;
    }

    /**
     * Get current locale script
     *
     * @return string Returns current locale script
     */
    public function getScript()
    {
        return $this->supportedLocales[$this->currentLocale]['script'];
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
     * Get current language for links
     *
     * @return string Returns current locale
     */
    public function getLanguage()
    {
        return ($this->hideDefaultLocaleInURL && $this->currentLocale == $this->defaultLocale) ? '' : $this->currentLocale;
    }

    /**
     * Get localized url
     *
     * @return string
     */
    public function getLocalizedURL($slug = null, $locale = null)
    {
        if ($slug) {
            $slugs = explode('/', $slug);
        } else {
            $slugs = ['/'];
        }

        $this->setSubdomain();
        $locale = $locale ?: $this->getLanguage();
        $url = ($locale == $this->getDefault() ? '' : $locale);
        $path = '';
        foreach ($slugs as $slug) {
            $url .= '/' . \Lang::get($this->subdomain . '/routes.' . $path . $slug . '.slug', [], $locale);
            $path .= $slug . './.';
        }
        return rtrim($url, '/');
    }

    /**
     * Match current slug recursively
     *
     * @return string Returns localized slugs
     */
    public function matchRecursive($routes)
    {
        foreach ($routes as $slug => $route) {
            if ($this->getLanguage()) {
                $match = $route['slug'];
            } else {
                $match = $slug;
            }

            if ($match == head($this->slugs)) {
                array_shift($this->slugs);

                if (count($this->slugs) > 0) {
                    if (isset($route['/'])) {
                        $return = $this->matchRecursive($route['/']);
                        if ($return) {
                            $return[] = $slug;
                            return $return;
                        }
                        return false;
                    }
                } else {
                    $return[] = $slug;
                    return $return;
                }
            }
        }
        return false;
    }

    /**
     * Get languages url
     *
     * @return string
     */
    public function getLocaleURL($locale)
    {
        $this->setSubdomain();
        $this->routes = \Lang::get($this->subdomain . '/routes', [], ($locale == $this->getDefault() ? $this->getLanguage() : $locale));
        $this->slugs = explode('/', \Slug::getSlug());
        $slugPath = $this->matchRecursive($this->routes);
        if ($slugPath) {
            $slugString = rtrim(implode('/', array_reverse($slugPath)), '/');
        } else {
            $slugString = null;
        }

        return $this->getLocalizedURL($slugString, $locale);
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
            $this->currentLocale = $locale;
        } else {
            // if the first segment/locale passed is not valid the locale could be taken by the browser depending on your configuration
            $locale = null;

            // if we reached this point and hideDefaultLocaleInURL is true we have to assume we are routing to a defaultLocale route.
            if ($this->hideDefaultLocaleInURL) {
                $this->currentLocale = $this->defaultLocale;
            } elseif ($this->useAcceptLanguageHeader) { // but if hideDefaultLocaleInURL is false && useAcceptLanguageHeader is true, we have to retrieve it from the browser...
                $this->currentLocale = $this->negotiateLanguage();
            } else { // or just get application default locale
                $this->currentLocale = $this->defaultLocale;
            }
        }

        \App::setLocale($this->currentLocale);
    }

    /**
     * Set the subdomain
     *
     * @return string
     */
    public function setSubdomain()
    {
        return $this->subdomain = explode('.', \Request::getHost())[0];
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
            return $this->defaultLocale;
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

        return $this->defaultLocale;
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
