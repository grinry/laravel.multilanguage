<?php
/**
 * @author Rytis Grincevicius <rytis@kiberzauras.com>
 * @license MIT
 */
namespace Kiberzauras\MultiLanguage;

use Illuminate\Routing\UrlGenerator as LaravelUrlGenerator;
use App;

class UrlGenerator extends LaravelUrlGenerator
{
    /**
     * @param $path
     * @param array $extra
     * @param null $secure
     * @return mixed
     */
    public function to($path, $extra = [], $secure = null)
    {
        return $this->toUrl($path, $extra, $secure);
    }

    /**
     * @param $path
     * @param array $extra
     * @param null $secure
     * @return mixed
     */
    private function toUrl($path, $extra = [], $secure = null)
    {
        // First we will check if the URL is already a valid URL. If it is we will not
        // try to generate a new one but will simply return the URL as is, which is
        // convenient since developers do not always have to check if it's valid.
        if ($this->isValidUrl($path)) {
            return $path;
        }
        if (array_key_exists('language', $extra)) {
            $path = $extra['language'] . '/' . trim($path, '/');
            unset($extra['language']);
        } else {
            $path = App::getLocale() . '/' . trim($path, '/');
        }
        $scheme = $this->getScheme($secure);

        $extra = $this->formatParameters($extra);

        $tail = implode('/', array_map(
                'rawurlencode', (array) $extra)
        );

        // Once we have the scheme we will compile the "tail" by collapsing the values
        // into a single string delimited by slashes. This just makes it convenient
        // for passing the array of parameters to this URL as a list of segments.
        $root = $this->getRootUrl($scheme);

        return $this->trimUrl($root, $path, $tail);
    }

    /**
     * @param \Illuminate\Routing\Route $route
     * @param mixed $parameters
     * @param bool $absolute
     * @return string
     */
    protected function toRoute($route, $parameters, $absolute)
    {
        $parameters = $this->formatParameters($parameters);

        $language = App::getLocale();
        if (array_key_exists('language', $parameters)) {
            $language = $parameters['language'];
            unset($parameters['language']);
        }

        $domain = $this->getRouteDomain($route, $parameters);

        $uri = strtr(rawurlencode($this->addQueryString($this->trimUrl(
            $root = $this->replaceRoot($route, $domain, $parameters),
            $language,
            $this->replaceRouteParameters($route->uri(), $parameters)
        ), $parameters)), $this->dontEncode);

        return $absolute ? $uri : '/'.ltrim(str_replace($root, '', $uri), '/');
    }
}
