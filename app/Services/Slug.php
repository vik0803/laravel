<?php

namespace App\Services;

use Stringy\StaticStringy;

class Slug
{
    protected $slug;
    protected $routeSlug;

    /**
     * Creates new instance.
     */
    public function __construct()
    {
        $this->setSlug(urldecode(\Request::path()));
    }

    /**
     * Get the Request slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug ?: '/';
    }

    /**
     * Set the Request slug
     *
     * @return string
     */
    public function setSlug($slug)
    {
        return $this->slug = StaticStringy::removeLeft($slug, \Locales::getCurrent() . '/');
    }

    /**
     * Get the Request slugs
     *
     * @return array
     */
    public function getSlugs()
    {
        return $this->getSlug() == '/' ? [] : explode('/', $this->getSlug());
    }

    /**
     * Get the Route slug
     *
     * @return string
     */
    public function getRouteSlug()
    {
        return $this->routeSlug ?: '/';
    }

    /**
     * Set the Route slug
     *
     * @return string
     */
    public function setRouteSlug($slug)
    {
        return $this->routeSlug = StaticStringy::removeLeft($slug, \Locales::getCurrent() . '/');
    }

    /**
     * Get the Route slugs
     *
     * @return array
     */
    public function getRouteSlugs()
    {
        return $this->getRouteSlug() == '/' ? [] : explode('/', $this->getRouteSlug());
    }

    /**
     * Compares given route slug with current route slug
     *
     * @param  string  $slug
     * @param  int  $index
     * @return string Returns class name
     */
    public function isActive($slug, $index = null)
    {
        $routeSlug = $index ? $this->getRouteSlugs()[--$index] : $this->getRouteSlug();
        return $routeSlug == $slug ? 'active' : null;
    }

    /**
     * Compares given route slug with current route slug
     *
     * @param  string  $slug
     * @param  int  $index
     * @return string Returns HTML class attribute
     */
    public function isActiveClass($slug, $index = null)
    {
        $class = $this->isActive($slug, $index);
        return $class ? ' class="' . $class . '"' : null;
    }
}
