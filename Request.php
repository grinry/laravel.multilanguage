<?php
/**
 * @author Rytis Grincevicius <rytis@kiberzauras.com>
 * @license MIT
 */
namespace Kiberzauras\MultiLanguage;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Illuminate\Http\Request as LaravelRequest;

class Request extends LaravelRequest
{
    /** There we will check if first segment of request uri is one our supported
     * languages, then we will set it to locale and remove from request, this way
     * there will be no need to configure our routes.php file for any of languages.
     * @return LaravelRequest
     *
     * @todo If request doesn't have language, it would be great to check browser language or location.
     */
    public static function capture()
    {
        $params = require_once config_path() . DIRECTORY_SEPARATOR . 'multiLanguage.php';

        if (!empty($params['array'])) {
            $uri = trim($_SERVER['REQUEST_URI'], '/');
            $lang = strstr($uri, '/', true);
            if (in_array($lang, $params['array'])) {
                // for accessing /en/page/page
                $_SERVER['REQUEST_URI'] = strstr($uri, '/');
                define('Language', $lang);
            } elseif (in_array($uri, $params['array'])) {
                // for accessing /, /en, and /en/ pages
                $_SERVER['REQUEST_URI'] = '/';
                define('Language', $uri);
            }
        }
        defined('Language') || define('Language', !empty($params['default'])?$params['default']:'en');

        static::enableHttpMethodParameterOverride();
        return static::createFromBase(SymfonyRequest::createFromGlobals());
    }
}
