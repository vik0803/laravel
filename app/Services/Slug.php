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
    protected $parameters = null;

    /**
     * Creates new instance.
     */
    public function __construct()
    {
        $this->setSlug(rtrim(ltrim(StaticStringy::replace(urldecode(\Request::path()), \Locales::getLanguage(), ''), '/'), '/'));
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
                    } elseif (isset($route[$this->method])) {
                        $r = $route[$this->method];
                        if (is_array($r)) {
                            if (isset($r['parameters'])) {
                                $this->setParameters($r['parameters']);

                                $paramsCount = count(explode('/', $r['parameters']));
                                $slug = implode('/', array_slice(explode('/', $this->getSlug()), 0, -$paramsCount));
                                $this->setSlug($slug);
                            }
                            $this->setController(Str::title($this->subdomain) . '\\' . $r['controller']);
                            return true;
                        }
                    }
                } elseif (isset($route[$this->method]) && !is_array($route[$this->method])) {
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
    public function setSlug($slug)
    {
        return $this->slug = $slug;
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

    /**
     * Get the route parameters
     *
     * @return string
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set the route parameters
     *
     * @return void
     */
    public function setParameters($parameters)
    {
        $this->parameters = '/' . $parameters;
    }
}
