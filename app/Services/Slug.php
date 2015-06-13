<?php

namespace App\Services;

use Illuminate\Support\Str;
use Stringy\StaticStringy;

class Slug
{
    protected $controller;
    protected $subdomains;
    protected $routes;
    protected $subdomain;
    protected $slug;
    protected $slugs;
    protected $method;

    /**
     * Creates new instance.
     */
    public function __construct()
    {
        $this->setSlug();
    }

    /**
     * Match slug
     *
     * @return boolean
     */
    public function match()
    {
        $this->subdomain = explode('.', \Request::getHost())[0];
        $this->routes = \Lang::get($this->subdomain . '/routes');
        $this->subdomains = \Config::get('subdomains');
        $this->slugs = explode('/', $this->getSlug());
        $this->method = Str::lower(\Request::method());

        if (in_array($this->subdomain, $this->subdomains)) {
            return $this->matchRecursive($this->routes);
        } else {
            return false;
        }
    }

    /**
     * Match current slug recursively
     *
     * @return string Returns controller@method
     */
    public function matchRecursive($routes)
    {
        foreach ($routes as $route) {
            if ($route['slug'] == head($this->slugs)) {
                array_shift($this->slugs);

                if (count($this->slugs) > 0) {
                    if (isset($route['/'])) {
                        return $this->matchRecursive($route['/']);
                    }
                    return false;
                } elseif (isset($route[$this->method])) {
                    $this->setController(Str::title($this->subdomain) . '\\' . $route[$this->method]);
                    return true;
                }
                return false;
            }
        }
        return false;
    }

    /**
     * Get the Request slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set the Request slug
     *
     * @return string
     */
    public function setSlug()
    {
        return $this->slug = rtrim(ltrim(StaticStringy::replace(urldecode(\Request::path()), \Locales::getLanguage(), ''), '/'), '/');
    }

    /**
     * Get the Request method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get the controller@method
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set the controller@method
     *
     * @return void
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }
}
